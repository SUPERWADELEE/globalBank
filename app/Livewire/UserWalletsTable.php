<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Wallet;
use App\Models\PlatformWallet;
use Filament\Notifications\Notification;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use App\Models\Deposit;
use App\Enums\DepositStatus;
use App\Models\Withdraw;
use App\Enums\WithdrawStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserWalletsTable extends Component
{
    public bool $showConfirmation = false;
    public string $confirmAction = '';
    public ?int $selectedWalletId = null;
    public array $amounts = [];
    public bool $isProcessing = false; // 防止重複提交

    public User $user;

    protected $listeners = ['executeConfirmedAction'];

    public function render()
    {
        return view('livewire.user-wallets-table');
    }

    public function makeDeposit($walletId)
    {
        // 防止重複提交
        if ($this->isProcessing) {
            $this->notifyError(__('user.operation_in_progress'));
            return;
        }

        $this->isProcessing = true;

        try {
            // 使用 Redis 分散式鎖防止併發
            $lockKey = "wallet_operation_{$walletId}_" . Auth::id();
            $result = Cache::lock($lockKey, 10)->get(function () use ($walletId) {
                return $this->processDeposit($walletId);
            });

            // 如果無法獲得鎖
            if ($result === null) {
                $this->notifyError(__('user.operation_locked'));
                return;
            }
        } catch (\Exception $e) {
            $this->notifyError(__('user.operation_failed'));
            Log::error('Deposit failed: ' . $e->getMessage(), [
                'wallet_id' => $walletId,
                'user_id' => Auth::id(),
                'amount' => $this->amounts[$walletId] ?? null
            ]);
        } finally {
            $this->isProcessing = false;
        }
    }

    private function processDeposit($walletId)
    {
        return DB::transaction(function () use ($walletId) {
            // 使用悲觀鎖鎖定錢包記錄
            $wallet = Wallet::lockForUpdate()->findOrFail($walletId);
            $amount = $this->validateAmount($this->amounts[$walletId] ?? null);
            if (!$amount) return 'validation_failed'; // 返回字符串而不是 false

            // 鎖定平台錢包
            $platformWallet = PlatformWallet::lockForUpdate()
                ->where('currency_code_id', $wallet->currency_code_id)
                ->firstOrFail();

            $this->updatePlatformBalance($platformWallet, $amount, add: true);
            $this->updateWalletBalance($wallet, $amount, add: true);
            $this->addDeposit($wallet, $amount);
            $this->finalizeTransaction(__('user.deposit_success'));
            
            return 'success';
        });
    }

    public function makeWithdraw($walletId)
    {
        // 防止重複提交
        if ($this->isProcessing) {
            $this->notifyError(__('user.operation_in_progress'));
            return;
        }

        $this->isProcessing = true;

        try {
            // 使用 Redis 分散式鎖防止併發
            $lockKey = "wallet_operation_{$walletId}_" . Auth::id();
            $result = Cache::lock($lockKey, 10)->get(function () use ($walletId) {
                return $this->processWithdraw($walletId);
            });

            // 如果無法獲得鎖
            if ($result === null) {
                $this->notifyError(__('user.operation_locked'));
                return;
            }
        } catch (\Exception $e) {
            $this->notifyError(__('user.operation_failed'));
            Log::error('Withdraw failed: ' . $e->getMessage(), [
                'wallet_id' => $walletId,
                'user_id' => Auth::id(),
                'amount' => $this->amounts[$walletId] ?? null
            ]);
        } finally {
            $this->isProcessing = false;
        }
    }

    private function processWithdraw($walletId)
    {
        return DB::transaction(function () use ($walletId) {
            // 使用悲觀鎖鎖定錢包記錄
            $wallet = Wallet::lockForUpdate()->findOrFail($walletId);
            $amount = $this->validateAmount($this->amounts[$walletId] ?? null);
            if (!$amount) return 'validation_failed'; // 返回字符串而不是 false

            $walletBalance = $this->decimal($wallet->balance);
            
            // 鎖定平台錢包
            $platformWallet = PlatformWallet::lockForUpdate()
                ->where('currency_code_id', $wallet->currency_code_id)
                ->firstOrFail();
            
            $platformBalance = $this->decimal($platformWallet->amount);

            if ($platformBalance->isLessThan($amount)) {
                $this->notifyError(__('platform_wallet.insufficient_balance'));
                return 'platform_insufficient'; // 返回字符串而不是 false
            }

            if ($walletBalance->isLessThan($amount)) {
                $this->notifyError(__('user.insufficient_balance'));
                return 'user_insufficient'; // 返回字符串而不是 false
            }

            $this->updatePlatformBalance($platformWallet, $amount, add: false);
            $this->updateWalletBalance($wallet, $amount, add: false);
            $this->addWithdraw($wallet, $amount);
            $this->finalizeTransaction(__('user.withdraw_success'));
            
            return 'success';
        });
    }

    private function addDeposit(Wallet $wallet, BigDecimal $amount): void
    {
        Deposit::create([
            'order_number' => $this->generateOrderNumber('D'),
            'user_id' => $wallet->user_id,
            'currency_code_id' => $wallet->currency_code_id,
            'amount' => $amount,
            'status' => DepositStatus::Success,
            'admin_user_id' => Auth::id(),
        ]);
    }

    private function addWithdraw(Wallet $wallet, BigDecimal $amount): void
    {
        Withdraw::create([
            'order_number' => $this->generateOrderNumber('W'),
            'user_id' => $wallet->user_id,
            'currency_code_id' => $wallet->currency_code_id,
            'amount' => $amount,
            'status' => WithdrawStatus::Success,
            'admin_user_id' => Auth::id(),
        ]);
    }

    private function generateOrderNumber(string $prefix): string
    {
        return $prefix . now()->format('YmdHis') . strtoupper(Str::random(4));
    }

    private function validateAmount($rawAmount): ?BigDecimal
    {
        if (!is_numeric($rawAmount) || $rawAmount <= 0) {
            $this->notifyError(__('user.invalid_amount'));
            return null;
        }

        // 檢查小數位數是否超過2位
        $decimalPlaces = strlen(substr(strrchr($rawAmount, "."), 1));
        if (strpos($rawAmount, '.') !== false && $decimalPlaces > 2) {
            $this->notifyError(__('user.amount_too_many_decimals'));
            return null;
        }

        return BigDecimal::of($rawAmount)->toScale(2, RoundingMode::DOWN);
    }

    private function getPlatformWallet($currencyCodeId): PlatformWallet
    {
        return PlatformWallet::where('currency_code_id', $currencyCodeId)->firstOrFail();
    }

    private function decimal($value): BigDecimal
    {
        return BigDecimal::of($value)->toScale(6, RoundingMode::DOWN);
    }

    private function notifyError(string $message): void
    {
        Notification::make()->title($message)->danger()->send();
    }

    private function notifySuccess(string $message): void
    {
        Notification::make()->title($message)->success()->send();
    }

    private function updateWalletBalance(Wallet $wallet, BigDecimal $amount, bool $add): void
    {
        $current = $this->decimal($wallet->balance);
        $wallet->balance = $add
            ? $current->plus($amount)->__toString()
            : $current->minus($amount)->__toString();
        $wallet->save();
    }

    private function updatePlatformBalance(PlatformWallet $wallet, BigDecimal $amount, bool $add): void
    {
        $current = $this->decimal($wallet->amount);
        $wallet->amount = $add
            ? $current->plus($amount)->__toString()
            : $current->minus($amount)->__toString();
        $wallet->save();
    }

    private function finalizeTransaction(string $message): void
    {
        $this->reset('amounts');
        $this->notifySuccess($message);
    }

    public function confirmDeposit($walletId): void
    {
        if ($this->isProcessing) {
            $this->notifyError(__('user.operation_in_progress'));
            return;
        }
        
        $this->dispatch('open-modal', id: 'wallet-confirm-modal');  // 打開
        $this->selectedWalletId = $walletId;
        $this->confirmAction = 'deposit';
        $this->showConfirmation = true;
    }

    public function confirmWithdraw($walletId): void
    {
        if ($this->isProcessing) {
            $this->notifyError(__('user.operation_in_progress'));
            return;
        }
        
        $this->dispatch('open-modal', id: 'wallet-confirm-modal');  // 打開
        $this->selectedWalletId = $walletId;
        $this->confirmAction = 'withdraw';
        $this->showConfirmation = true;
    }

    public function executeConfirmedAction(): void
    {
        if ($this->isProcessing) {
            $this->notifyError(__('user.operation_in_progress'));
            return;
        }
        
        if ($this->confirmAction === 'deposit') {
            $this->makeDeposit($this->selectedWalletId);
        }

        if ($this->confirmAction === 'withdraw') {
            $this->makeWithdraw($this->selectedWalletId);
        }

        $this->dispatch('close-modal', id: 'wallet-confirm-modal'); // 關閉

        $this->showConfirmation = false;
    }
    public function closeModal()
    {
        $this->dispatch('close-modal', id: 'wallet-confirm-modal'); // 關閉
        $this->showConfirmation = false;
    }

    public function testConcurrency()
    {
        // 簡單的併發測試方法
        $this->notifySuccess('併發測試：請快速連續點擊入金或出金按鈕來測試併發控制效果');
        
        // 記錄測試開始
        Log::info('Concurrency test started', [
            'user_id' => Auth::id(),
            'timestamp' => now()
        ]);
    }

}

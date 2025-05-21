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

class UserWalletsTable extends Component
{
    public bool $showConfirmation = false;
    public string $confirmAction = '';
    public ?int $selectedWalletId = null;
    public array $amounts = [];

    public User $user;

    protected $listeners = ['executeConfirmedAction'];

    public function render()
    {
        return view('livewire.user-wallets-table');
    }

    public function makeDeposit($walletId)
    {
        $wallet = Wallet::findOrFail($walletId);
        $amount = $this->validateAmount($this->amounts[$walletId] ?? null);
        if (!$amount) return;

        $platformWallet = $this->getPlatformWallet($wallet->currency_code_id);

        $this->updatePlatformBalance($platformWallet, $amount, add: true);
        $this->updateWalletBalance($wallet, $amount, add: true);
        $this->addDeposit($wallet, $amount);
        $this->finalizeTransaction(__('user.deposit_success'));
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

    public function makeWithdraw($walletId)
    {
        $wallet = Wallet::findOrFail($walletId);
        $amount = $this->validateAmount($this->amounts[$walletId] ?? null);
        if (!$amount) return;

        $walletBalance = $this->decimal($wallet->balance);
        $platformWallet = $this->getPlatformWallet($wallet->currency_code_id);
        $platformBalance = $this->decimal($platformWallet->amount);

        if ($platformBalance->isLessThan($amount)) {
            $this->notifyError(__('platform_wallet.insufficient_balance'));
            return;
        }

        if ($walletBalance->isLessThan($amount)) {
            $this->notifyError(__('user.insufficient_balance'));
            return;
        }

        $this->updatePlatformBalance($platformWallet, $amount, add: false);
        $this->updateWalletBalance($wallet, $amount, add: false);
        $this->addWithdraw($wallet, $amount);
        $this->finalizeTransaction(__('user.withdraw_success'));
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

        return BigDecimal::of($rawAmount)->toScale(6, RoundingMode::DOWN);
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
        $this->dispatch('open-modal', id: 'wallet-confirm-modal');  // 打開
        $this->selectedWalletId = $walletId;
        $this->confirmAction = 'deposit';
        $this->showConfirmation = true;
    }

    public function confirmWithdraw($walletId): void
    {
        $this->dispatch('open-modal', id: 'wallet-confirm-modal');  // 打開
        $this->selectedWalletId = $walletId;
        $this->confirmAction = 'withdraw';
        $this->showConfirmation = true;
    }

    public function executeConfirmedAction(): void
    {
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

}

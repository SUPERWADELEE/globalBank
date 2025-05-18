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

class UserWalletsTable extends Component
{
    public array $amounts = [];

    public User $user;
    public Wallet $wallet;
    public Deposit $deposit;
    public Withdraw $withdraw;
    public function render()
    {
        return view('livewire.user-wallets-table');
    }

    public function makeDeposit($walletId)
    {
        $wallet = $this->user->wallets()->findOrFail($walletId);
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
        $this->deposit = Deposit::create([
            'user_id' => $this->user->id,
            'currency_code_id' => $wallet->currency_code_id,
            'amount' => $amount,
            'status' => DepositStatus::Success,
            'admin_user_id' => auth()->user()->id,
        ]);
    }

    public function makeWithdraw($walletId)
    {
        $wallet = $this->user->wallets()->findOrFail($walletId);
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
        $this->withdraw = Withdraw::create([
            'user_id' => $this->user->id,
            'currency_code_id' => $wallet->currency_code_id,
            'amount' => $amount,
            'status' => WithdrawStatus::Success,
            'admin_user_id' => auth()->user()->id,
        ]);
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
}

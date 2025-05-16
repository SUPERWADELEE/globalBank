<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Wallet;

class UserWalletsTable extends Component
{
    public array $amounts = [];

    public User $user;

    public function render()
    {
        return view('livewire.user-wallets-table');
    }

    public function deposit($walletId)
    {
        $wallet = $this->user->wallets()->findOrFail($walletId);
        $amount = floatval($this->amounts[$walletId] ?? 0);

        if ($amount > 0) {
            $wallet->balance += $amount;
            $wallet->save();
            $this->reset('amounts');
        }
    }

    public function withdraw($walletId)
    {
        $wallet = $this->user->wallets()->findOrFail($walletId);
        $amount = floatval($this->amounts[$walletId] ?? 0);

        if ($amount > 0 && $wallet->balance >= $amount) {
            $wallet->balance -= $amount;
            $wallet->save();
            $this->reset('amounts');
        }
    }
}

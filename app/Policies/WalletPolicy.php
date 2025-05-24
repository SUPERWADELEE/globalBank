<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Wallet;
use Illuminate\Auth\Access\HandlesAuthorization;

class WalletPolicy
{
    use HandlesAuthorization;

    /**
     * 是否可以查看錢包頁面
     */
    public function view(AdminUser $user): bool
    {
        return $user->can('view_user_wallet');
    }

    /**
     * 是否可以對指定幣別入金
     */
    public function deposit(AdminUser $user, Wallet $wallet): bool
    {
        $permission = 'deposit_' . $wallet->currencyCode->code . '_user_wallet';
        return $user->can($permission);
    }

    /**
     * 是否可以對指定幣別出金
     */
    public function withdraw(AdminUser $user, Wallet $wallet): bool
    {
        $permission = 'withdraw_' . $wallet->currencyCode->code . '_user_wallet';
        return $user->can($permission);
    }
}
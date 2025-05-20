<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Wallet;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountSettingPolicy
{
    use HandlesAuthorization;

    /**
     * 是否可以查看帳號設定
     */
    public function view(AdminUser $user): bool
    {
        return $user->can('view_account_settings');
    }

    /**
     * 是否可以編輯帳號設定
     */
    public function edit(AdminUser $user): bool
    {
        return $user->can('edit_account_settings');
    }
}
<?php

namespace App\Policies;

use App\Models\AdminUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserWalletLogsPolicy
{
    use HandlesAuthorization;

    /**
     * 是否可以查看錢包頁面
     */
    public function view(AdminUser $user): bool
    {
        return $user->can('view_user_wallet_logs');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // 會員錢包
            'view_user_wallet',
            'deposit_USDT_user_wallet',  'withdraw_USDT_user_wallet',
            'deposit_KRW_user_wallet',   'withdraw_KRW_user_wallet',
            'deposit_JPY_user_wallet',   'withdraw_JPY_user_wallet',
            'deposit_SGD_user_wallet',   'withdraw_SGD_user_wallet',

            // 會員錢包日誌
            'view_user_wallet_logs',

            // 帳戶設定
            'view_account_settings', 'edit_account_settings',

            // 匯率
            'edit_usdt_rate', 'edit_jpy_rate', 'edit_sgd_rate', 'edit_krw_rate',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name);
        }
    }
}

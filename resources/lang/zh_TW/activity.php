<?php

return [
    'log_description' => '操作員 :causer 對 :subject 進行了 :event 操作',
    'subject_modules' => [
        'App\\Models\\AdminUser' => '系統設置',
        'App\\Models\\AdminIpWhitelist' => '系統設置',
        'App\\Models\\Activity' => '系統設置',
        'App\\Models\\Rate' => '匯率設置',
        'App\\Models\\PlatformWallet' => '平台錢包',
        'App\\Models\\Withdraw' => '出金',
        'App\\Models\\Deposit' => '入金',
        'App\\Models\\User' => '用戶',
        'App\\Models\\Wallet' => '帳務操作',
    ],
    'event_names' => [
        'created' => '新增',
        'updated' => '編輯',
        'deleted' => '刪除',
    ],
];
<?php

return [
    'log_description' => '操作員 :causer 對 :subject 進行了 :event 操作',
    'subject_modules' => [
        'App\\Models\\AdminUser' => '系統設置',
        'App\\Models\\AdminIpWhitelist' => '系統設置',
        'App\\Models\\Activity' => '系統設置',
        'App\\Models\\Rate' => '匯率設置',
        'App\\Models\\PlatformWallet' => '平台錢包',
        'App\\Models\\Withdraw' => '系統設置',
        'App\\Models\\Deposit' => '系統設置',
        'App\\Models\\User' => '用戶',
        'App\\Models\\Wallet' => '帳務操作',
        'App\\Models\\AdminUserTeam' => '系統設置',
        'App\\Models\\DepositLocation' => '入金地址管理',
        'Spatie\\Permission\\Models\\Role' => '系統設置',
        'App\\Models\\DepositOrder' => '訂單管理',
        'App\\Models\\WithdrawOrder' => '訂單管理',
    ],
    'event_names' => [
        'created' => '新增',
        'updated' => '編輯',
        'deleted' => '刪除',
    ],
    'admin_user_property' => [
        'name' => '名稱',
        'email' => '電子郵件',
        'password' => '密碼',
        'job_title' => '職稱',
        'team_id' => '單位',
        'locale' => '語言',

    ],
    'log_description' => '操作員 :causer 對 :subject 進行了 :event 操作',
    'rate_updated' => '操作員 :causer :event 了匯率：:from → :to（:details）',
    'rate_buy_rate_change' => '買入匯率從 :old 變更為 :new',
    'rate_sell_rate_change' => '賣出匯率從 :old 變更為 :new',
    'deposit_created' => '操作員 :causer 為會員 :member 入金 :amount :currency',
    'deposit_status_updated' => '操作員 :causer 把會員 :member 的入金訂單狀態從 :old_status 變更為 :new_status',
    'deposit_amount_updated' => '操作員 :causer 把會員 :member 的入金訂單金額從 :old 變更為 :new',
    'withdraw_created' => '操作員 :causer 為會員 :member 出金 :amount :currency',
    'deposit_location_created' => '操作員 :causer 新增了入金地址 :location。',
    'deposit_location_updated' => '操作員 :causer 更新了入金地址資料的:subject 的:changes。',
    'deposit_location_change_line' => ':field 從「:old」變更為「:new」',
    'deposit_location_fields' => [
        'currency_code_id' => '幣別',
        'location' => '入金地址',
        'channel' => '渠道',
        'status' => '狀態',
    ],
    'user_created' => '操作員 :causer 新增了會員 :member',
    'user_updated' => '操作員 :causer 更新了會員 :member 的資料：:changes。',
    'user_change_line' => ':field 從「:old」變更為「:new」',
    'user_fields' => [
        'name' => '姓名',
        'email' => '信箱',
        'phone' => '電話',
        'status' => '狀態',
        'username' => '帳號',
        'register_location' => '註冊地點',
        'user_level_id' => '會員等級',
        // 其他欄位如 password / otp_secret 可選擇不顯示
    ],
    'user_status' => [
        'active' => '正常',
        'inactive' => '凍結',
    ],

    'platform_wallet_created' => '操作員 :causer 為會員 :member 入金 :amount :currency',
    'user_deposit' => '操作員 :causer 為會員 :member 入金 :amount :currency',
    'wallet_created' => '操作員 :causer 為會員 :member 入金 :amount :currency',
   
    'admin_user_deleted' => '操作員 :causer 刪除了操作員 :admin_user',
    'admin_user_team_created' => '操作員 :causer 新增了 :new_team 單位',
    'admin_user_team_name_updated' => '把單位名稱從 :old 變更為 :new',
    'admin_user_team_description_updated' => '把描述從 :old 變更為 :new',
    'admin_user_team_no_change' => '操作員 :causer 沒有變更任何資料',
    'admin_user_team_deleted' => '操作員 :causer 刪除了 :old_team 單位',
    'ip_white_list_created' => '操作員 :causer 新增了 IP 白名單 :new_ip_address',
    'ip_white_list_updated' => '操作員 :causer 更新了 IP 白名單 :old_ip_address -> :new_ip_address',
    'ip_white_list_deleted' => '操作員 :causer 刪除了 IP 白名單 :old_ip_address',
    'deposit_location_property' => [
        'currency_code_id' => '幣別',
        'location' => '地址',
        'channel' => '渠道',
        'status' => '狀態',
    ],
    'role_created' => '操作員 :causer 建立了角色 :role，並指派權限：:permissions。',
    'role_updated' => '操作員 :causer 更新了角色 :role 的資料：:changes。',
    'role_change_line' => ':field 從「:old」變更為「:new」',
    'withdraw_status_updated' => '操作員 :causer 把會員 :member 的出金訂單狀態從 :old_status 變更為 :new_status',
    'withdraw_amount_updated' => '操作員 :causer 把會員 :member 的出金訂單金額從 :old 變更為 :new',
    'status' => [
        'pending' => '待處理',
        'success' => '已完成',
        'failed' => '失敗',
    ],
    'withdraw_tx_hash_updated' => '操作員 :causer 把會員 :member 的出金訂單tx_hash從 :old 變更為 :new',
    'default_description' => '操作員 :causer 對 :subject 進行了 :event 操作',
     // 每一行「欄位變更」的格式
     'admin_user_change_line' => ':field 由 :old 變更為 :new',

     // 組合所有變更後的最終訊息
     'admin_user_updated'     => '操作員 :causer 更新了操作員 :admin_user 的 :changes',
     
     // 建立時的訊息
     'admin_user_created'     => '操作員 :causer 新增了操作員 :admin_user',
     
     // 萬一沒匹配到其他事件
     'default_admin_user_operation'      => '操作員 :causer 對 :subject 進行了 :event 操作。',
     'default_operation' => '操作員 :causer 更新了:subject。',
      // 建立時
    'role_created'              => '操作員 :causer 建立了角色「:role」，並分配權限：:permissions',
    // 更新時：新增
    'role_change_added'         => '新增權限：:permissions',
    // 更新時：移除
    'role_change_removed'       => '移除權限：:permissions',
    // 更新後
    'role_updated'              => '操作員 :causer 更新了角色「:role」，變更內容：:changes',
    // 如果更新事件卻沒有任何權限變化
    'role_no_permission_changes'=> '沒有任何權限變更',
    // 通用 fallback
    'log_description'           => '操作員 :causer 對 :subject 進行了 :event 操作',
];
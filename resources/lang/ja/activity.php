<?php

return [
    'log_description' => '操作員 :causer 對操作員 :subject 進行了 :event 操作',
    'subject_modules' => [
        'App\\Models\\AdminUser' => '系統設置',
        'App\\Models\\AdminIpWhitelist' => '系統設置',
        'App\\Models\\Activity' => '系統設置',
    ],
    'event_names' => [
        'created' => '新增',
        'updated' => '編輯',
        'deleted' => '刪除',
    ],
];
<?php

namespace App\Enums;



enum UserStatusEnum: int   // ← 用 int
{
    case Active = 1;       // 啟用
    case Frozen = 0;       // 凍結

    public function label(): string
    {
        return match ($this) {
            self::Active => __('user.active'),
            self::Frozen => __('user.frozen'),
        };
    }
}
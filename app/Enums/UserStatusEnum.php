<?php

namespace App\Enums;

enum UserStatusEnum: int
{
    case Active = 1;
    case Frozen = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::Active => __('user.active'),
            self::Frozen => __('user.frozen'),
        };
    }
}
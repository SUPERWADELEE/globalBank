<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Frozen = 'frozen';

    public function getLabel(): string
    {
        return match ($this) {
            self::Active => '正常',
            self::Frozen => '凍結',
        };
    }
}
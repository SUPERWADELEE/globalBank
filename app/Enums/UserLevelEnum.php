<?php

namespace App\Enums;

enum UserLevelEnum: string
{
    case VIP1 = 'VIP1';
    case VIP2 = 'VIP2';
    case VIP3 = 'VIP3';
    case VIP4 = 'VIP4';
    case VIP5 = 'VIP5';
    case VIP6 = 'VIP6';
    case VIP7 = 'VIP7';
    case VIP8 = 'VIP8';

    public function getLabel(): string
    {
        return match ($this) {
            self::VIP1 => 'VIP1',
            self::VIP2 => 'VIP2',
            self::VIP3 => 'VIP3',
            self::VIP4 => 'VIP4',
            self::VIP5 => 'VIP5',
            self::VIP6 => 'VIP6',
            self::VIP7 => 'VIP7',
            self::VIP8 => 'VIP8',
        };
    }
}
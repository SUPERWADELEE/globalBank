<?php

namespace App\Enums;


enum DepositCode: int
{
    case USDT = 4;


    public function label(): string
    {
        return match ($this) {
            self::USDT => __('deposit_code.usdt'),
        };
    }
}

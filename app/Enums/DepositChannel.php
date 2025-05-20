<?php

namespace App\Enums;


enum DepositChannel: string
{
    case TRC20 = 'TRC20';
   

    public function label(): string
    {
        return match ($this) {
            self::TRC20 => __('deposit_channel.trc20'),
        };
    }
}

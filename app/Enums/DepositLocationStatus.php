<?php

namespace App\Enums;


enum DepositLocationStatus: string
{
    case Enable = '1';
    case Disable = '0';


    public function label(): string
    {
        return match ($this) {
            self::Enable => __('deposit_location.location.enable'),
            self::Disable => __('deposit_location.location.disable'),
        };
    }
}

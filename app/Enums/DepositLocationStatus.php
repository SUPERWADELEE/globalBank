<?php

namespace App\Enums;


enum DepositLocationStatus: string
{
    case Enable = 'Enable';
    case Disable = 'Disable';


    public function label(): string
    {
        return match ($this) {
            self::Enable => __('deposit_location_status.enable'),
            self::Disable => __('deposit_location_status.disable'),
        };
    }
}

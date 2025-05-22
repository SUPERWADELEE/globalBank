<?php

namespace App\Enums;

enum ExchangeOrderStatus: int
{
    case Pending = 0;
    case Success = 1;
    case Failed = 2;

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('activity.status.pending'),
            self::Success => __('activity.status.success'),
            self::Failed => __('activity.status.failed'),
        };
    }
}

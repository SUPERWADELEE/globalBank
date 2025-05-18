<?php

namespace App\Enums;

enum DepositStatus: int
{
    case Pending = 0;
    case Success = 1;
    case Failed = 2;

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('deposit.status.pending'),
            self::Success => __('deposit.status.success'),
            self::Failed => __('deposit.status.failed'),
        };
    }
}

<?php

namespace App\Enums;

enum WithdrawStatus: int
{
    case Pending = 0;
    case Success = 1;
    case Failed = 2;

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('withdraw.pending'),
            self::Success => __('withdraw.success'),
            self::Failed => __('withdraw.failed'),
        };
    }
}

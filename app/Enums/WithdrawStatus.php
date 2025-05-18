<?php

namespace App\Enums;

enum WithdrawStatus: int
{
    case Pending = 0;
    case Success = 1;
    case Failed = 2;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\DepositChannel;
use App\Enums\DepositLocationStatus;
use App\Enums\DepositCode;

class DepositLocation extends Model
{
    protected $fillable = [
        'currency_code_id',
        'location',
        'channel',
        'status',
    ];

    protected $casts = [
        'channel' => DepositChannel::class,
        'status' => DepositLocationStatus::class,
        'currency_code_id' => DepositCode::class,
    ];

    public function currencyCode()
    {
        return $this->belongsTo(CurrencyCode::class);
    }
}

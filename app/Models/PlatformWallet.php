<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
class PlatformWallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'currency_code_id',
        'amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => MoneyCast::class,
    ];

    /**
     * Get the currency code that owns the platform wallet.
     */
    public function currencyCode()
    {
        return $this->belongsTo(CurrencyCode::class, 'currency_code_id', 'id');
    }
}

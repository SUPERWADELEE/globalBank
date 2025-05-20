<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

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
    public static function getTotalInUSDT(): float
    {
        $wallets = self::with('currencyCode')->get();

        $total = BigDecimal::zero();

        foreach ($wallets as $wallet) {
            $code = $wallet->currencyCode->code;

            // 如果就是 USDT，不用轉換
            if ($code === 'USDT') {
                $total = $total->plus($wallet->amount);
                continue;
            }

            $rate = Rate::whereHas('toCurrency', fn($q) => $q->where('code', $code))
                ->whereHas('fromCurrency', fn($q) => $q->where('code', 'USDT'))
                ->first();
            if ($rate?->sell_rate) {

                $converted = BigDecimal::of($wallet->amount)->multipliedBy($rate->sell_rate);
                $total = $total->plus($converted);
            }
        }

        return $total->toScale(6, RoundingMode::DOWN)->toFloat();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rate',
        'is_active' => 'boolean',
    ];

    /**
     * Get the from currency.
     */
    public function fromCurrency()
    {
        return $this->belongsTo(CurrencyCode::class, 'from_currency', 'code');
    }

    /**
     * Get the to currency.
     */
    public function toCurrency()
    {
        return $this->belongsTo(CurrencyCode::class, 'to_currency', 'code');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'currency_code_id',
        'amount',
        'status',
        'tx_hash',
        'related_order_id',
        'admin_user_id',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount'
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin user that processed the transaction.
     */
    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class);
    }

    /**
     * Get the currency code associated with this transaction.
     */
    public function currencyCode()
    {
        return $this->belongsTo(CurrencyCode::class, 'currency_code_id', 'id');
    }

    /**
     * Get the related deposit if type is deposit.
     */
    public function deposit()
    {
        if ($this->type === 'deposit') {
            return $this->belongsTo(Deposit::class, 'related_order_id');
        }
        return null;
    }

    /**
     * Get the related withdraw if type is withdraw.
     */
    public function withdraw()
    {
        if ($this->type === 'withdraw') {
            return $this->belongsTo(Withdraw::class, 'related_order_id');
        }
        return null;
    }

    /**
     * Get the related exchange order if type is exchange.
     */
    public function exchangeOrder()
    {
        if ($this->type === 'exchange') {
            return $this->belongsTo(ExchangeOrder::class, 'related_order_id');
        }
        return null;
    }
    protected static function booted()
    {
        static::creating(function ($transaction) {
            $transaction->order_number = self::generateOrderNumber();
        });
    }
    public static function generateOrderNumber(): string
    {
        $prefix = 'TX';
        $date = now()->format('Ymd');
        $lastNumber = self::whereDate('created_at', now())->count() + 1;
        return $prefix . $date . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
    }
}

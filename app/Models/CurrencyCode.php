<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * Get the platform wallets for this currency code.
     */
    public function platformWallets()
    {
        return $this->hasMany(PlatformWallet::class, 'currency_code_id', 'id');
    }

    /**
     * Get the deposits for this currency code.
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'currency_code_id', 'id');
    }

    /**
     * Get the withdraws for this currency code.
     */
    public function withdraws()
    {
        return $this->hasMany(Withdraw::class, 'currency_code_id', 'id');
    }

    /**
     * Get the transactions for this currency code.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'currency_code_id', 'id');
    }
}

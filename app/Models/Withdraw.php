<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
class Withdraw extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'withdraws';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'currency_code_id',
        'amount',
        'status',
        'tx_hash',
        'admin_user_id',
        'description',
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
     * Get the user that owns the withdraw.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin user that processed the withdraw.
     */
    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class);
    }

    /**
     * Get the currency code associated with this withdraw.
     */
    public function currencyCode()
    {
        return $this->belongsTo(CurrencyCode::class, 'currency_code_id', 'id');
    }
}

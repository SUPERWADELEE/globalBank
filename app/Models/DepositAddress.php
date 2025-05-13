<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositAddress extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'currency_code_id',
        'address',
    ];

    /**
     * Get the user that owns the deposit address.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

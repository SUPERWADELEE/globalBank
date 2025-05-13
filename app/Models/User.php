<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'otp_secret',
        'member_level_id',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'otp_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }
    
    /**
     * Get the member level associated with the user.
     */
    public function memberLevel()
    {
        return $this->belongsTo(MemberLevel::class);
    }
    
    /**
     * Get the wallets for the user.
     */
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }
    
    /**
     * Get the deposit addresses for the user.
     */
    public function depositAddresses()
    {
        return $this->hasMany(DepositAddress::class);
    }
}

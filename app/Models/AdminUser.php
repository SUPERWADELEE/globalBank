<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class AdminUser extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get the deposits approved by this admin user.
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'admin_user_id');
    }

    /**
     * Get the withdraws approved by this admin user.
     */
    public function withdraws()
    {
        return $this->hasMany(Withdraw::class, 'admin_user_id');
    }

    /**
     * Get the transactions approved by this admin user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'admin_user_id');
    }

    /**
     * Get the admin IP whitelist entries created by this admin user.
     */
    public function ipWhitelists()
    {
        return $this->hasMany(AdminIpWhitelist::class, 'admin_user_id');
    }
}

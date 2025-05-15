<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminIpWhitelist extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_ip_whitelist';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ip_address',
        'admin_user_id',
    ];

    protected $casts = [
        'ip_address' => 'string',
        'admin_user_id' => 'integer',
    ];

    /**
     * Get the admin user that created this IP whitelist entry.
     */
    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class);
    }

    public function getIpAttribute(): string
    {
        return $this->ip_address;
    }
}

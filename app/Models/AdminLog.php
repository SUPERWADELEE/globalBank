<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $fillable = [
        'admin_user_id',
        'action',
        'content',
        'ip',
        'user_agent',
    ];
    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class);
    }
    public function getOldDataAttribute($value)
    {
        return json_decode($value, true);
    }
    public function getNewDataAttribute($value)
    {
        return json_decode($value, true);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUserTeam extends Model
{
    protected $fillable = ['name', 'description'];

    public function adminUsers()
    {
        return $this->hasMany(AdminUser::class);
    }
}

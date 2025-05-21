<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
class AdminUserTeam extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = ['name', 'description'];

    public function adminUsers()
    {
        return $this->hasMany(AdminUser::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        $adminUser = Auth::user()->name;
        return LogOptions::defaults()
            ->logAll()
            ->logOnly(['name', 'description'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) use ($adminUser) {
                $subjectName = $this->name;
                return __('activity.log_description', [
                    'causer' => $adminUser,
                    'subject' => $subjectName,
                    'event' => $eventName
                ]);
            })
            ->dontSubmitEmptyLogs();
    }
}

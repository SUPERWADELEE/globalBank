<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;
class AdminUser extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

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
        'job_title',
        'team_id',
    ];
    protected $guard_name = 'admin';

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


    public function getActivitylogOptions(): LogOptions
    {
        $adminUser = Auth::user()->name;
        return LogOptions::defaults()
            ->logAll()
            ->logOnly(['name', 'email', 'locale', 'job_title', 'team_id'])
            ->logOnlyDirty(true)
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
    public function activities()
    {
        return $this->morphMany(Activity::class, 'causer');
    }
    public function getAllowedCurrencyCodesForRate(): array
    {
        $map = [
            'edit_usdt_rate' => 'USDT',
            'edit_sgd_rate' => 'SGD',
            'edit_krw_rate' => 'KRW',
        ];
    
        return collect($map)
            ->filter(function ($code, $permission) {
                return $this->can($permission);
            })
            ->values()
            ->all();
    }
    public function team()
    {
        return $this->belongsTo(AdminUserTeam::class);
    }
}

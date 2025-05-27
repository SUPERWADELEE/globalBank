<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AdminUser;
class AdminIpWhitelist extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_ip_whitelists';

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
    public function getActivitylogOptions(): LogOptions
    {
        $adminUser = Auth::user() ? Auth::user()->name : 'System';
        return LogOptions::defaults()
            ->logAll()
            ->logOnly(['ip_address'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) use ($adminUser) {
                $subjectName = $this->ip_address;
                return __('activity.log_description', [
                    'causer' => $adminUser,
                    'subject' => $subjectName,
                    'event' => $eventName
                ]);
            })
            ->dontSubmitEmptyLogs();
    }
}

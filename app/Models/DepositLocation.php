<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\DepositChannel;
use App\Enums\DepositLocationStatus;
use App\Enums\DepositCode;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;

class DepositLocation extends Model
{
    use LogsActivity;

    protected $fillable = [
        'currency_code_id',
        'location',
        'channel',
        'status',
    ];

    protected $casts = [
        'channel' => DepositChannel::class,
        'status' => DepositLocationStatus::class,
        'currency_code_id' => DepositCode::class,
    ];

    public function currencyCode()
    {
        return $this->belongsTo(CurrencyCode::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        $adminUser = Auth::user()->name;
        return LogOptions::defaults()
            ->logAll()
            ->logOnly(['location', 'channel', 'status'])
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
    protected static function booted(): void
    {
        static::saving(function ($model) {
            if ($model->status === \App\Enums\DepositLocationStatus::Enable) {
                // 把其他全改成 disable
                static::where('id', '!=', $model->id)
                    ->where('status', \App\Enums\DepositLocationStatus::Enable)
                    ->update(['status' => \App\Enums\DepositLocationStatus::Disable]);
            }
        });
    }
}

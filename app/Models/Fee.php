<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;
use App\Casts\MoneyCast;

class Fee extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'currency_code_id',
        'amount',
    ];

    protected $casts = [
        'amount' => MoneyCast::class,
    ];

    public function currencyCode()
    {
        return $this->belongsTo(CurrencyCode::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        // 檢查是否有已登入的用戶，如果沒有（如在 seeder 中）則使用預設值
        $adminUser = Auth::user() ? Auth::user()->name : 'System';
        return LogOptions::defaults()
            ->logAll()
            ->logOnly(['amount'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) use ($adminUser) {
                $subjectName = $this->name ?? 'Fee';
                return __('activity.log_description', [
                    'causer' => $adminUser,
                    'subject' => $subjectName,
                    'event' => $eventName
                ]);
            })
            ->dontSubmitEmptyLogs();
    }
}
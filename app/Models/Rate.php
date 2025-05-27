<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;

class Rate extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'from_currency_id',
        'to_currency_id',
        'sell_rate',
        'buy_rate',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sell_rate' => MoneyCast::class,
        'buy_rate' => MoneyCast::class,
    ];

    /**
     * Get the from currency.
     */
    public function fromCurrency()
    {
        return $this->belongsTo(CurrencyCode::class, 'from_currency_id'); // 第三個參數省略，預設是 'id'
    }

    public function toCurrency()
    {
        return $this->belongsTo(CurrencyCode::class, 'to_currency_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        
        $adminUser = Auth::user() ? Auth::user()->name : 'System';
        return LogOptions::defaults()
            ->logAll()
            ->logOnly(['buy_rate', 'sell_rate'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) use ($adminUser) {
                $subjectName = $this->name ?? 'Rate';
                return __('activity.log_description', [
                    'causer' => $adminUser,
                    'subject' => $subjectName,
                    'event' => $eventName
                ]);
            })
            ->dontSubmitEmptyLogs();
    }
    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;

class ExchangeOrder extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'from_currency',
        'to_currency',
        'amount_from',
        'amount_to',
        'rate',
        'status',
        'admin_user_id',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount_from' => MoneyCast::class,
        'amount_to' => MoneyCast::class,
        'rate' => MoneyCast::class,
    ];

    /**
     * Get the user that owns the exchange order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin user that processed the exchange order.
     */
    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class);
    }

    /**
     * Get the from currency code.
     */
    public function fromCurrency()
    {
        return $this->belongsTo(CurrencyCode::class, 'from_currency', 'code');
    }

    /**
     * Get the to currency code.
     */
    public function toCurrency()
    {
        return $this->belongsTo(CurrencyCode::class, 'to_currency', 'code');
    }
    public function getActivitylogOptions(): LogOptions
    {
        $adminUser = Auth::user()->name;
        return LogOptions::defaults()
            ->logAll()
            ->logOnly(['amount_from', 'amount_to', 'rate'])
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

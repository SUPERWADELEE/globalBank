<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CurrencyCode;
use Spatie\Activitylog\Models\Activity;


class Wallet extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'currency_code_id',
        'balance',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance'
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currencyCode()
    {
        return $this->belongsTo(CurrencyCode::class);
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */


    public function getActivitylogOptions(): LogOptions
    {
        $adminUser = Auth::check() ? Auth::user()->name : 'System';
        return LogOptions::defaults()
            ->logAll()
            ->logOnly(['balance'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) use ($adminUser) {
                $currencyCode = $this->currencyCode?->code ?? '';
                return "Wallet {$currencyCode} {$eventName} by {$adminUser}";
            })
            ->dontSubmitEmptyLogs();
    }

    public function activityLogs()
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;

class CurrencyCode extends Model
{
    use HasFactory, LogsActivity;
    const USDT_ID = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * Get the platform wallets for this currency code.
     */
    public function platformWallets()
    {
        return $this->hasMany(PlatformWallet::class, 'currency_code_id', 'id');
    }

    /**
     * Get the deposits for this currency code.
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'currency_code_id', 'id');
    }

    /**
     * Get the withdraws for this currency code.
     */
    public function withdraws()
    {
        return $this->hasMany(Withdraw::class, 'currency_code_id', 'id');
    }

    /**
     * Get the transactions for this currency code.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'currency_code_id', 'id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        
        $adminUser = Auth::user() ? Auth::user()->name : 'System';
        return LogOptions::defaults()
            ->logAll()
            ->logOnly(['code', 'name'])
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

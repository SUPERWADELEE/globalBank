<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
use App\Enums\DepositStatus;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;

class Deposit extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'deposits';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'currency_code_id',
        'amount',
        'tx_hash',
        'status',
        'admin_user_id',
        'description',
        'order_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => MoneyCast::class,
        'status' => DepositStatus::class,
    ];

    /**
     * Get the user that owns the deposit.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin user that processed the deposit.
     */
    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class);
    }

    /**
     * Get the currency code associated with this deposit.
     */
    public function currencyCode()
    {
        return $this->belongsTo(CurrencyCode::class, 'currency_code_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $adminUser = Auth::user()->name;
        return LogOptions::defaults()
            ->logAll()
            ->logOnly(['amount', 'status'])
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

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Wallet;
use App\Services\OperateUserWalletLogService;
use Spatie\Activitylog\Models\Activity;
use App\Models\AdminUser;
use App\Models\CurrencyCode;

class UserWalletsLogTable extends Component
{
    public array $amounts = [];

    public User $user;
    public Wallet $wallet;

    public function render()
    {
        $activityLogs = $this->getFormattedWalletActivityLogs();

        return view('livewire.user-wallets-log-table', [
            'activityLogs' => $activityLogs,
        ]);
    }

    private function getFormattedWalletActivityLogs()
    {
        return $this->user->wallets()->with('activityLogs')->get()->map(function ($wallet) {
            $wallet->activityLogs = $wallet->activityLogs->map(fn ($log) => $this->formatActivityLog($log));
            return $wallet;
        });
    }

    private function formatActivityLog($activityLog)
    {
        $activityLog->causer_name = $activityLog->causer_type === AdminUser::class
            ? AdminUser::find($activityLog->causer_id)?->name
            : null;

        $oldBalance = $activityLog->properties['old']['balance'] ?? 0;
        $newBalance = $activityLog->properties['attributes']['balance'] ?? 0;

        $activityLog->balance_change = $newBalance - $oldBalance;

        $currencyCode = Wallet::find($activityLog->subject_id)?->currencyCode?->code ?? '';

        $activityLog->content = $newBalance > $oldBalance
            ? $currencyCode . " " . __('user.deposit')
            : $currencyCode . " " . __('user.withdraw');

        return $activityLog;
    }
}

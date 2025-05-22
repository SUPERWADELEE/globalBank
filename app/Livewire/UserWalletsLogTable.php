<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Wallet;
use App\Models\AdminUser;
use Brick\Math\BigDecimal;

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
            $wallet->activityLogs = $wallet->activityLogs->map(fn($log) => $this->formatActivityLog($log));
            return $wallet;
        });
    }

    private function formatActivityLog($activityLog)
    {
        $activityLog->causer_name = $activityLog->causer_type === AdminUser::class
            ? AdminUser::find($activityLog->causer_id)?->name
            : null;
        $activityLog->causer_job_title = $activityLog->causer_type === AdminUser::class
            ? AdminUser::find($activityLog->causer_id)?->job_title
            : null;

        $oldBalance = BigDecimal::of($activityLog->properties['old']['balance'] ?? 0);
        $newBalance = BigDecimal::of($activityLog->properties['attributes']['balance'] ?? 0);

        $activityLog->balance_change = $newBalance->minus($oldBalance)->toScale(2); // 保留 2 位小數


        $currencyCode = Wallet::find($activityLog->subject_id)?->currencyCode?->code ?? '';

        $activityLog->content = $newBalance > $oldBalance
            ? $currencyCode . " " . __('user.deposit')
            : $currencyCode . " " . __('user.withdraw');

        return $activityLog;
    }
}

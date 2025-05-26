<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Wallet;
use App\Models\AdminUser;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

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
        $allActivityLogs = collect();

        $this->user->wallets()->with(['activityLogs' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->get()->each(function ($wallet) use ($allActivityLogs) {
            $wallet->activityLogs->each(function ($log) use ($allActivityLogs) {
                $formattedLog = $this->formatActivityLog($log);
                $allActivityLogs->push($formattedLog);
            });
        });

        return $allActivityLogs->sortByDesc('created_at');
    }

    // 拿到金額變動及操作內容
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

        $activityLog->balance_change = $newBalance->minus($oldBalance)->toScale(2, RoundingMode::HALF_UP);

        $currencyCode = Wallet::find($activityLog->subject_id)?->currencyCode?->code ?? '';

        $activityLog->content = $newBalance > $oldBalance
            ? $currencyCode . " " . __('user.deposit')
            : $currencyCode . " " . __('user.withdraw');

        return $activityLog;
    }
}

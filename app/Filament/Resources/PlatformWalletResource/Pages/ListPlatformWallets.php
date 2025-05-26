<?php

namespace App\Filament\Resources\PlatformWalletResource\Pages;

use Livewire\Attributes\On;
use App\Filament\Resources\PlatformWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Session;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class ListPlatformWallets extends ListRecords
{
    protected static string $resource = PlatformWalletResource::class;
    protected $minutes = 2;
    public ?\Carbon\Carbon $refreshDisabledUntil = null;
    public function mount(): void
    {
        parent::mount();
        // 从 Session 恢复禁用狀態
        $disabledUntil = Session::get('platform_wallet_refresh_disabled_until');
        if ($disabledUntil) {
            $this->refreshDisabledUntil = Carbon::parse($disabledUntil);

            // 如果時間已過期，清除 Session
            if (now()->gte($this->refreshDisabledUntil)) {
                $this->refreshDisabledUntil = null;
                Session::forget('platform_wallet_refresh_disabled_until');
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('deposit')
                ->label(__('platform_wallet.renew'))
                ->action('manualRefresh')
                ->disabled(fn() => $this->isRefreshDisabled())
        ];
    }

    public function getTitle(): string
    {
        return __('platform_wallet.title.platform_wallet');
    }

    public function manualRefresh()
    {
        if ($this->isRefreshDisabled()) {
            Notification::make()
                ->title(__('platform_wallet.refresh_still_disabled'))
                ->warning()
                ->send();
            return;
        }

        // 重新設置按鈕時間
        $this->refreshDisabledUntil = now()->addMinutes($this->minutes);
        Session::put('platform_wallet_refresh_disabled_until', $this->refreshDisabledUntil->toDateTimeString());
        $this->dispatch('refresh');
        Notification::make()
            ->title(__('platform_wallet.refreshed_successfully') . ' ' . __('platform_wallet.refresh_disabled', ['minutes' => $this->minutes]))
            ->success()
            ->send();
    }

    /**
     * 检查刷新按钮是否被禁用
     */
    private function isRefreshDisabled(): bool
    {
        if ($this->refreshDisabledUntil === null) {
            return false;
        }

        if (now()->gte($this->refreshDisabledUntil)) {
            $this->refreshDisabledUntil = null;
            Session::forget('platform_wallet_refresh_disabled_until');
            return false;
        }

        return true;
    }
}

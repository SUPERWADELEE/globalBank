<?php

namespace App\Filament\Resources\PlatformWalletResource\Pages;

use Livewire\Attributes\On;
use App\Filament\Resources\PlatformWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Session;
use Filament\Notifications\Notification;

class ListPlatformWallets extends ListRecords
{
    protected static string $resource = PlatformWalletResource::class;
    protected $seconds = 2;

    public function mount(): void
    {
        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('deposit')
                ->label(__('platform_wallet.renew'))
                ->action('manualRefresh')
                ->disabled(fn() => $this->refreshDisabledUntil !== null && now()->lt($this->refreshDisabledUntil))
                ->tooltip(function () {
                    if ($this->refreshDisabledUntil !== null && now()->lt($this->refreshDisabledUntil)) {
                        $seconds = now()->diffInSeconds($this->refreshDisabledUntil);
                        return __('platform_wallet.refresh_disabled', ['seconds' => $seconds]);
                    }

                    return null;
                }),
        ];
    }

    public function getTitle(): string
    {
        return __('platform_wallet.title.platform_wallet');
    }

    public ?\Carbon\Carbon $refreshDisabledUntil = null;

    public function manualRefresh()
    {
        // 设置 2 分钟内不可再点
        $this->refreshDisabledUntil = now()->addSeconds($this->seconds);

        // 将禁用时间存储在会话中
        Session::put('platform_wallet_refresh_disabled_until', $this->refreshDisabledUntil->toDateTimeString());

        // 重新取得资料
        $this->dispatch('refresh'); // Livewire 事件会刷新 table

        // 显示通知
        Notification::make()
            ->title(__('platform_wallet.refreshed_successfully') . ' ' . __('platform_wallet.refresh_disabled', ['seconds' => $this->seconds]))
            ->success()
            ->send();
    }
}

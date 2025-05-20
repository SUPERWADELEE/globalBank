<?php

namespace App\Filament\Resources\PlatformWalletResource\Pages;

use App\Filament\Resources\PlatformWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlatformWallets extends ListRecords
{
    protected static string $resource = PlatformWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('deposit')
                ->label(__('platform_wallet.renew'))
                ->url(route('filament.admin.resources.platform-wallets.index')),

        ];
    }
    public function getTitle(): string
    {
        return __('platform_wallet.title.platform_wallet');
    }
}

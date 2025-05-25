<?php

namespace App\Filament\Resources\DepositLogResource\Pages;

use App\Filament\Resources\DepositLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepositLogs extends ListRecords
{
    protected static string $resource = DepositLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('出金紀錄')
                ->url(route('filament.admin.resources.withdraw-logs.index'))
                ->label(__('withdraw.navigation.withdraw_record')),
            Actions\Action::make('入金紀錄')
                ->url(route('filament.admin.resources.deposit-logs.index'))
                ->label(__('deposit.navigation.deposit_record')),
            Actions\Action::make('匯款紀錄')
                ->url(route('filament.admin.resources.exchange-logs.index'))
                ->label(__('exchange.navigation.exchange_record')),

        ];
    }

    public function getTitle(): string
    {
        return __('deposit.title');
    }
}

<?php

namespace App\Filament\Resources\WithdrawResource\Pages;

use App\Filament\Resources\WithdrawResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWithdraws extends ListRecords
{
    protected static string $resource = WithdrawResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('出金紀錄')
            ->url(route('filament.admin.resources.withdraws.index'))
            ->label(__('withdraw.navigation.withdraw_record')),
            Actions\Action::make('入金紀錄')
            ->url(route('filament.admin.resources.deposit-logs.index'))
            ->label(__('deposit.navigation.deposit_record')),

        ];
    }

    public function getTitle(): string
    {
        return __('withdraw.title');
    }
}

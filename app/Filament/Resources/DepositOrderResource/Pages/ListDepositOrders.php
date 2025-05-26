<?php

namespace App\Filament\Resources\DepositOrderResource\Pages;

use App\Filament\Resources\DepositOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Deposit;
use App\Models\Withdraw;
use App\Enums\DepositStatus;
use App\Enums\WithdrawStatus;

class ListDepositOrders extends ListRecords
{
    protected static string $resource = DepositOrderResource::class;

    protected function getHeaderActions(): array
    {
        $depositProcessingCount = Deposit::where('status', DepositStatus::Pending)->count();
        $withdrawProcessingCount = Withdraw::where('status', WithdrawStatus::Pending)->count();
        return [
            Actions\Action::make('入金訂單')
                ->url(route('filament.admin.resources.deposit-orders.index'))
                ->label(__('deposit.order.title') . " ({$depositProcessingCount})"),
            Actions\Action::make('出金訂單')
                ->url(route('filament.admin.resources.withdraw-orders.index'))
                ->label(__('withdraw.order.title') . " ({$withdrawProcessingCount})"),
            Actions\Action::make('重新整理')
                ->url(route('filament.admin.resources.deposit-orders.index'))
                ->label(__('common.refresh')),
        ];
    }

    public function getTitle(): string
    {
        return __('deposit.order.title');
    }
}

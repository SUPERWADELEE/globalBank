<?php

namespace App\Filament\Resources\WithdrawOrderResource\Pages;

use App\Filament\Resources\WithdrawOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Withdraw;
use App\Models\Deposit;
use App\Enums\WithdrawStatus;
use App\Enums\DepositStatus;

class ListWithdrawOrders extends ListRecords
{
    protected static string $resource = WithdrawOrderResource::class;

    protected function getHeaderActions(): array
    {
        $withdrawProcessingCount = Withdraw::where('status', WithdrawStatus::Pending)->count();
        $depositProcessingCount = Deposit::where('status', DepositStatus::Pending)->count();

        return [
            Actions\Action::make('deposit')
                ->url(route('filament.admin.resources.deposit-orders.index'))
                ->label(__('deposit.order.title') . " ({$depositProcessingCount})"),

            Actions\Action::make('withdraw')
                ->url(route('filament.admin.resources.withdraw-orders.index'))
                ->label(__('withdraw.order.title') . " ({$withdrawProcessingCount})"),
            Actions\Action::make('重新整理')
                ->url(route('filament.admin.resources.deposit-orders.index'))
                ->label(__('deposit.refresh')),
        ];
    }
    public function getTitle(): string
    {
        return __('withdraw.order.title');
    }
}

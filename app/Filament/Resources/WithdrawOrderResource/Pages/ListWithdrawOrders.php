<?php

namespace App\Filament\Resources\WithdrawOrderResource\Pages;

use App\Filament\Resources\WithdrawOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWithdrawOrders extends ListRecords
{
    protected static string $resource = WithdrawOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
    public function getTitle(): string
    {
        return __('withdraw.order.title');
    }
}

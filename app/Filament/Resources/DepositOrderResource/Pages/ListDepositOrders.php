<?php

namespace App\Filament\Resources\DepositOrderResource\Pages;

use App\Filament\Resources\DepositOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepositOrders extends ListRecords
{
    protected static string $resource = DepositOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function getTitle(): string
    {
        return __('deposit.order.title');
    }
}

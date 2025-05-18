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

        ];
    }

    public function getTitle(): string
    {
        return __('deposit.title');
    }
}

<?php

namespace App\Filament\Resources\ExchangeLogResource\Pages;

use App\Filament\Resources\ExchangeLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExchangeLog extends EditRecord
{
    protected static string $resource = ExchangeLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\DepositLogResource\Pages;

use App\Filament\Resources\DepositLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDepositLog extends EditRecord
{
    protected static string $resource = DepositLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

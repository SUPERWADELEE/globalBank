<?php

namespace App\Filament\Resources\DepositLocationResource\Pages;

use App\Filament\Resources\DepositLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDepositLocation extends EditRecord
{
    protected static string $resource = DepositLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

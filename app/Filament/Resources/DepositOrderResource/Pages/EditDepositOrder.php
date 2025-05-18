<?php

namespace App\Filament\Resources\DepositOrderResource\Pages;

use App\Filament\Resources\DepositOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDepositOrder extends EditRecord
{
    protected static string $resource = DepositOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

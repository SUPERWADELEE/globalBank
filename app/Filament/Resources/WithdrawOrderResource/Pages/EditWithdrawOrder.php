<?php

namespace App\Filament\Resources\WithdrawOrderResource\Pages;

use App\Filament\Resources\WithdrawOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWithdrawOrder extends EditRecord
{
    protected static string $resource = WithdrawOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

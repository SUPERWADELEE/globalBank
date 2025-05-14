<?php

namespace App\Filament\Resources\AdminIpWhiteListResource\Pages;

use App\Filament\Resources\AdminIpWhiteListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminIpWhiteList extends EditRecord
{
    protected static string $resource = AdminIpWhiteListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

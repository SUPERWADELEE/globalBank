<?php

namespace App\Filament\Resources\AdminUserTeamResource\Pages;

use App\Filament\Resources\AdminUserTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminUserTeam extends EditRecord
{
    protected static string $resource = AdminUserTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

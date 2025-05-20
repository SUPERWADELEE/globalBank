<?php

namespace App\Filament\Resources\AdminUserTeamResource\Pages;

use App\Filament\Resources\AdminUserTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminUserTeams extends ListRecords
{
    protected static string $resource = AdminUserTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('admin_user.team.create')),
        ];
    }
    public function getTitle(): string
    {
        return __('admin_user.team.title');
    }
}

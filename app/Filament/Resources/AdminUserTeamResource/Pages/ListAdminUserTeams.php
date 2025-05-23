<?php

namespace App\Filament\Resources\AdminUserTeamResource\Pages;

use App\Filament\Resources\AdminUserTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminUserTeams extends ListRecords
{
    protected static string $resource = AdminUserTeamResource::class;
    public function getHeading(): string
    {
        return __('system_management.title');
    }

    public function getSubheading(): string
    {
        return __('admin_user.team.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('admin_user.team.create'))->color('success'),
            Actions\Action::make('帳號管理')
                ->url(route('filament.admin.resources.admin-users.index'))
                ->label(__('admin_user.navigation.account_management')),
            Actions\Action::make('權限管理')
                ->url(route('filament.admin.resources.roles.index'))
                ->label(__('admin_user.navigation.role_management')),
            Actions\Action::make('IP白名單')
                ->url(route('filament.admin.resources.admin-ip-white-lists.index'))
                ->label(__('admin_user.navigation.ip_white_list')),
            Actions\Action::make('單位管理')
                ->url(route('filament.admin.resources.admin-user-teams.index'))
                ->label(__('admin_user.navigation.team_management')),
        ];
    }
    // public function getTitle(): string
    // {
    //     return __('admin_user.team.title');
    // }
}

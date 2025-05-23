<?php

namespace App\Filament\Resources\AdminIpWhiteListResource\Pages;

use App\Filament\Resources\AdminIpWhiteListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;



class ListAdminIpWhiteLists extends ListRecords
{
    protected static string $resource = AdminIpWhiteListResource::class;

    public function getHeading(): string
    {
        return __('system_management.title');
    }
    public function getSubheading(): string
    {
        return __('admin_user.navigation.ip_white_list');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make() 
                ->label(__('permissions.create_admin_ip_white_list'))
                ->color('success'),

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

    public function getTitle(): string
    {
        return __('admin_user.navigation.ip_white_list');
    }
   
}

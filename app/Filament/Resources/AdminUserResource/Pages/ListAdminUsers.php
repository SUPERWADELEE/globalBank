<?php

namespace App\Filament\Resources\AdminUserResource\Pages;

use App\Filament\Resources\AdminUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminUsers extends ListRecords
{
    protected static string $resource = AdminUserResource::class;

    public function getHeading(): string
    {
        return __('system_management.title');
    }
    public function getSubheading(): string
    {
        return __('admin_user.navigation.account_management');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make('新增')
                ->color('success'), // 這樣會顯示在表格上方

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
    // public function getHeader(): ?View
    // {
    //     return view('filament.custom-header');
    // }

    public function getTitle(): string
    {
        return __('admin_user.navigation.account_management');
    }
}

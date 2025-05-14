<?php

namespace App\Filament\Resources\AdminIpWhiteListResource\Pages;

use App\Filament\Resources\AdminIpWhiteListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;



class ListAdminIpWhiteLists extends ListRecords
{
    protected static string $resource = AdminIpWhiteListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make('新增'), // 這樣會顯示在表格上方

            Actions\Action::make('帳號管理')
                ->url(route('filament.admin.resources.admin-users.index')),
            Actions\Action::make('權限管理')
                ->url(route('filament.admin.resources.roles.index')),
            Actions\Action::make('IP白名單')
                ->url(route('filament.admin.resources.admin-ip-white-lists.index')),
        ];
    }

    public function getTitle(): string
    {
        return 'IP白名單';
    }
    public function getBreadcrumb(): string
    {
        return 'IP白名單'; // 改這裡就會變成 "帳號管理 > List"
    }
}

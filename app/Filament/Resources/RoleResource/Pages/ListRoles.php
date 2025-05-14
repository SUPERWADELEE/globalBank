<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

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
        return '權限管理';
    }
}

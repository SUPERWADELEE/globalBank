<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Pages\InstallInfo;
class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('user.create_user')),
            Actions\Action::make('goToInstallInfo')
            ->label(__('install_info.title'))
            ->color('primary')                                // 想要的顏色
            ->url(fn () => InstallInfo::getUrl())             // ★ 用閉包回傳網址
        ];
    }
    public function getTitle(): string
    {
        return __('user.user_management_query');
    }
}

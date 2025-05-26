<?php

namespace App\Filament\Resources\AdminLogResource\Pages;

use App\Filament\Resources\AdminLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminLogs extends ListRecords
{
    protected static string $resource = AdminLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
    public function getTitle(): string
    {
        return __('admin_user.activity_log.title');
    }}


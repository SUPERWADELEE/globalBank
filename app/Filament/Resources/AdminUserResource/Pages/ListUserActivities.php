<?php

namespace App\Filament\Resources\AdminUserResource\Pages;

use App\Filament\Resources\AdminUserResource;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;
use App\Filament\Resources\AdminLogResource;

class ListUserActivities extends ListActivities
{
    protected static string $resource = AdminLogResource::class;
    public function getTitle(): string
    {
        return __('admin_user.activity_log.title');
    }
}

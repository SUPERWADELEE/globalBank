<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class InstallInfo extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static string $view = 'filament.pages.install-info';


    function getTitle(): string
    {
        return __('install_info.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('install_info.navigation_label');
    }

    public static function getNavigationGroup(): string
    {
        return __('user.user_management');
    }
}

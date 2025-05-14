<?php

namespace App\Providers;

// app/Providers/FilamentLanguageServiceProvider.php

use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class FilamentLanguageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filament::serving(function () {
            // 添加偵錯信息
            Log::info('Current locale: ' . app()->getLocale());
            
            Filament::registerUserMenuItems([
                UserMenuItem::make()
                    ->label('🌐 繁體中文')
                    ->url(route('language.switch', ['locale' => 'zh_TW']))
                    ->visible(app()->getLocale() !== 'zh_TW'),

                UserMenuItem::make()
                    ->label('🌐 English')
                    ->url(route('language.switch', ['locale' => 'en']))
                    ->visible(app()->getLocale() !== 'en'),

                UserMenuItem::make()
                    ->label('🌐 日本語')
                    ->url(route('language.switch', ['locale' => 'ja']))
                    ->visible(app()->getLocale() !== 'ja'),
            ]);
        });
    }
}
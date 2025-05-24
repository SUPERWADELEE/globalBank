<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;
use Filament\View\PanelsRenderHook;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $locale = session('locale', config('app.locale'));
        app()->setLocale($locale);
        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_END,
            fn(): View => view('filament.partials.custom-header-icon')
        );

        // 針對匯出的驗證，覆寫覆聯的user對象為admin_user
        $this->app->bind(\Filament\Actions\Exports\Models\Export::class, \App\Models\Export::class);
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;
use Filament\View\PanelsRenderHook;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;
use App\Models\Wallet;
use App\Policies\UserWalletPolicy;
use App\Models\Rate;
use App\Policies\RatePolicy;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\AdminUser;

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

        Gate::policy(Rate::class,   RatePolicy::class);
        
        // 註冊自定義的 Export 模型
        $this->app->bind(\Filament\Actions\Exports\Models\Export::class, \App\Models\Export::class);
    }
}

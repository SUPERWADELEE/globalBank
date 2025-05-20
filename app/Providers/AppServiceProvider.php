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

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Wallet::class => \App\Policies\UserWalletPolicy::class,
    ];
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
        $this->createUserWalletPermissions();
        $this->createUserWalletLogsPermissions();
        $this->createAccountSettingsPermissions();
        $this->registerPolicies();
    }
    public function createUserWalletPermissions()
    {
        Permission::firstOrCreate(['name' => 'view_user_wallet']);
        Permission::firstOrCreate(['name' => 'deposit_USDT_user_wallet']);
        Permission::firstOrCreate(['name' => 'withdraw_USDT_user_wallet']);
        Permission::firstOrCreate(['name' => 'deposit_KRW_user_wallet']);
        Permission::firstOrCreate(['name' => 'withdraw_KRW_user_wallet']);
        Permission::firstOrCreate(['name' => 'deposit_JPY_user_wallet']);
        Permission::firstOrCreate(['name' => 'withdraw_JPY_user_wallet']);
        Permission::firstOrCreate(['name' => 'deposit_SGD_user_wallet']);
        Permission::firstOrCreate(['name' => 'withdraw_SGD_user_wallet']);
    }
    public function createUserWalletLogsPermissions()
    {
        Permission::firstOrCreate(['name' => 'view_user_wallet_logs']);
    }
    public function createAccountSettingsPermissions()
    {
        Permission::firstOrCreate(['name' => 'view_account_settings']);
        Permission::firstOrCreate(['name' => 'edit_account_settings']);
    }
    public function registerPolicies()
    {
        Gate::policy(Wallet::class, UserWalletPolicy::class);

    }
}

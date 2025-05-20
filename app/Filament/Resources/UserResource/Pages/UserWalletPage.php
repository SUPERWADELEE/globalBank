<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\Page;
use App\Models\User;
use App\Filament\Resources\UserResource;

class UserWalletPage extends Page
{
    protected static string $view = 'filament.resources.user-resource.pages.user-wallet-page';
    protected static string $resource = UserResource::class;
    public ?User $user = null;


    protected static ?string $title = '使用者錢包';

    public function mount($record): void
    {
        $this->user = User::with('wallets')->findOrFail($record);
        $this->authorize('view_user_wallet', $this->user);
    }

    public function getTitle(): string
    {
        return __('user.user') . ' ' . $this->user->name . ' ' . __('user.wallet');
    }

    public static function getSlug(): string
    {
        return 'user-wallet-page';
    }
}

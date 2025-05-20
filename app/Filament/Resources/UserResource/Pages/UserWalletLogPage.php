<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\Page;
use App\Models\User;

class UserWalletLogPage extends Page
{
    protected static string $resource = UserResource::class;
    public ?User $user = null;
    protected static string $view = 'filament.resources.user-resource.pages.user-wallet-log-page';

    public function mount($record): void
    {
        $this->user = User::with('wallets')->findOrFail($record);
        $this->authorize('view_user_wallet_logs', $this->user);
    }

    public function getTitle(): string
    {
        return __('user.user') . ' ' . $this->user->name . ' ' . __('user.operation_log');
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlatformWalletResource\Pages;
use App\Filament\Resources\PlatformWalletResource\RelationManagers;
use App\Models\PlatformWallet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlatformWalletResource extends Resource
{
    protected static ?string $model = PlatformWallet::class;
    public static function getNavigationLabel(): string
    {
        return __('platform_wallet.navigation.platform_wallet');
    }

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        // 把所有幣別的平台餘額及code 及匯率撈出，
        // 把所有錢都轉換成USDT並加總
        $usdtTotal = PlatformWallet::getTotalInUSDT();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('currencyCode.code')
                    ->label(__('platform_wallet.USDT_balance')),

                Tables\Columns\TextColumn::make('amount')
                    ->label($usdtTotal),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlatformWallets::route('/'),
            'create' => Pages\CreatePlatformWallet::route('/create'),
        ];
    }
}

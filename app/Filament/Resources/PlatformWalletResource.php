<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlatformWalletResource\Pages;
use App\Models\PlatformWallet;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        $usdtTotal = PlatformWallet::getTotalInUSDT();
        $pollingTime = '300s';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('currencyCode.code')
                    ->label(__('platform_wallet.USDT_balance'))
                    ->html()
                    ->formatStateUsing(function ($state) {
                        $iconMap = [
                            'USDT' => '/images/coin/usdt.png',
                            'KRW' => '/images/coin/krw.png',
                            'JPY' => '/images/coin/jpy.png',
                            'SGD' => '/images/coin/sgd.png',
                        ];
                        $icon = $iconMap[$state] ?? '/images/coin/default.svg';
                        return "<img src='{$icon}' style='width: 24px;vertical-align:middle;margin-right:4px;' alt='{$state}'> {$state}";
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->label($usdtTotal),
            ])->poll($pollingTime);
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

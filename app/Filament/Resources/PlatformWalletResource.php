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
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('currencyCode.code'),
                Tables\Columns\TextColumn::make('amount'),
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
                //
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
            'edit' => Pages\EditPlatformWallet::route('/{record}/edit'),
        ];
    }
}

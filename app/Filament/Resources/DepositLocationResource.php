<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepositLocationResource\Pages;
use App\Models\DepositLocation;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Enums\DepositCode;
use App\Enums\DepositChannel;
use App\Enums\DepositLocationStatus;
use Filament\Tables\Columns\TextColumn;

class DepositLocationResource extends Resource
{
    protected static ?string $model = DepositLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationLabel(): string
    {
        return __('admin_user.deposit_location.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('currency_code_id')
                    ->label(__('admin_user.deposit_location.currency_code'))
                    ->options(DepositCode::class)
                    ->required(),
                TextInput::make('location')
                    ->label(__('admin_user.deposit_location.location'))
                    ->required(),
                Select::make('channel')
                    ->label(__('admin_user.deposit_location.channel'))
                    ->options(DepositChannel::class)
                    ->required(),
                Select::make('status')
                    ->label(__('admin_user.deposit_location.status'))
                    ->options(DepositLocationStatus::class)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('currencyCode.code')
                    ->label(__('admin_user.deposit_location.currency_code')),
                TextColumn::make('location')
                    ->label(__('admin_user.deposit_location.location')),
                TextColumn::make('channel')
                    ->label(__('admin_user.deposit_location.channel')),
                TextColumn::make('status')
                    ->label(__('admin_user.deposit_location.status')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListDepositLocations::route('/'),
            'create' => Pages\CreateDepositLocation::route('/create'),
            'edit' => Pages\EditDepositLocation::route('/{record}/edit'),
        ];
    }
}

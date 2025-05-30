<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeResource\Pages;
use App\Models\Fee;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;

class FeeResource extends Resource
{
    protected static ?string $model = Fee::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('amount')
                    ->label(__('usdt_setting.fee_amount'))
                    ->rules(['required','numeric','min:0','max:100','decimal:0,2','gt:0'])
                    ->markAsRequired()
            ]);
    }
    public static function getNavigationLabel(): string
    {
        return __('usdt_setting.navigation.fee_settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('usdt_setting.navigation.usdt_settings');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('currencyCode.code')
                    ->label(__('currency.code'))
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(__('usdt_setting.fee_amount'))
                    ->sortable(),
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListFees::route('/'),
            'create' => Pages\CreateFee::route('/create'),
            'edit' => Pages\EditFee::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['currencyCode']);
    }
}

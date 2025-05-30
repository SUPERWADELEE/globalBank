<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RateResource\Pages;
use App\Models\Rate;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use App\Models\CurrencyCode;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminUser;
use App\Policies\RatePolicy;
use Illuminate\Support\Facades\Gate;

class RateResource extends Resource
{
    protected static ?string $model = Rate::class;


    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    public static function getNavigationLabel(): string
    {
        return __('admin_user.navigation.rate_settings');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('from_currency_id')
                    ->label(__('rate.from_currency'))
                    ->options(CurrencyCode::all()->pluck('code', 'id'))
                    ->disabled(),
                Select::make('to_currency_id')
                    ->label(__('rate.to_currency'))
                    ->options(CurrencyCode::all()->pluck('code', 'id'))
                    ->disabled(),
                TextInput::make('sell_rate')
                    ->label(__('rate.sell_rate'))
                    ->rules(['required', 'numeric', 'min:0', 'decimal:0,2','gt:0'])
                    ->markAsRequired()
                    ->disabled(fn($record) => !$record || Gate::denies('edit', $record)),
                TextInput::make('buy_rate')
                    ->label(__('rate.buy_rate'))
                    ->rules(['required', 'numeric', 'min:0', 'decimal:0,2','gt:0'])
                    ->markAsRequired()
                    ->disabled(fn($record) => !$record || Gate::denies('edit', $record)),
            ]);
    }

    public static function table(Table $table): Table
    {
        $currencies = CurrencyCode::all()->pluck('code', 'id');
        return $table
            ->columns([
                TextColumn::make('fromCurrency.code')
                    ->label(__('rate.from_currency')),
                TextColumn::make('toCurrency.code')
                    ->label(__('rate.to_currency')),
                TextColumn::make('sell_rate')
                    ->label(__('rate.sell_rate')),
                TextColumn::make('buy_rate')
                    ->label(__('rate.buy_rate')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListRates::route('/'),
            'create' => Pages\CreateRate::route('/create'),
            'edit' => Pages\EditRate::route('/{record}/edit'),
        ];
    }
    
    // }
    // public static function getEloquentQuery(): Builder
    // {
    //     $allowed = Auth::user()->getAllowedCurrencyCodesForRate();

    //     return parent::getEloquentQuery()->whereHas('fromCurrency', function ($q) use ($allowed) {
    //         $q->whereIn('code', $allowed);
    //     });
    // }
}

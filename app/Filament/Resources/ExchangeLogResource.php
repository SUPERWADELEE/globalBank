<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExchangeLogResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Models\ExchangeOrder;
use Filament\Tables\Columns\TextColumn;
use App\Enums\ExchangeOrderStatus;
use Filament\Tables\Filters\SelectFilter;
use App\Models\CurrencyCode;
use Filament\Tables\Enums\FiltersLayout;
use App\Filament\Filters\CommonDateFilters;

class ExchangeLogResource extends Resource
{
    protected static ?string $model = ExchangeOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('transaction.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('exchange.title');
    }

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
            ->query(
                ExchangeOrder::query()->with(['user', 'fromCurrency', 'toCurrency'])
            )
            ->columns([
                TextColumn::make('order_number')->label(__('exchange.order_number')),
                TextColumn::make('user.name')->label(__('user.username')),
                TextColumn::make('buy_display')
                    ->label(__('user.buy'))
                    ->getStateUsing(function ($record) {
                        return number_format($record->amount_to, 2) . ' ' . ($record->toCurrency->code ?? '');
                    }),
                TextColumn::make('sell_display')
                    ->label(__('user.sell'))
                    ->getStateUsing(function ($record) {
                        return '-' . number_format($record->amount_from, 2) . ' ' . ($record->fromCurrency->code ?? '');
                    }),
                TextColumn::make('unit_price')
                    ->label(__('user.unit_price'))
                    ->getStateUsing(function ($record) {
                        return number_format($record->rate, 2);
                    }),
                TextColumn::make('status')
                    ->label(__('exchange.status'))
                    ->getStateUsing(function ($record) {
                        return ExchangeOrderStatus::from($record->status)->label();
                    })
                    ->badge()
                    ->color(fn($state) => match ($state instanceof ExchangeOrderStatus ? $state : ExchangeOrderStatus::from((int) $state)) {
                        ExchangeOrderStatus::Pending => 'warning',
                        ExchangeOrderStatus::Success => 'success',
                        ExchangeOrderStatus::Failed => 'danger',
                    }),
                TextColumn::make('created_at')->label(__('exchange.created_at')),

            ])
            ->filters([
                SelectFilter::make('from_currency_id')
                    ->label(__('exchange.buy_currency_code'))
                    ->options(CurrencyCode::pluck('code', 'id'))
                    ->native(false),

                SelectFilter::make('to_currency_id')
                    ->label(__('exchange.sell_currency_code'))
                    ->options(CurrencyCode::pluck('code', 'id'))
                    ->native(false),
                CommonDateFilters::dateRange(),
                CommonDateFilters::quickRange(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([]);
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
            'index' => Pages\ListExchangeLogs::route('/'),
            'create' => Pages\CreateExchangeLog::route('/create'),
            'edit' => Pages\EditExchangeLog::route('/{record}/edit'),
        ];
    }
}

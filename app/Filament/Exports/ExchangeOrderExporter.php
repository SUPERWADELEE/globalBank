<?php

namespace App\Filament\Exports;

use App\Models\ExchangeOrder;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\ExchangeOrderStatus;

class ExchangeOrderExporter extends Exporter
{
    protected static ?string $model = ExchangeOrder::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('order_number'),
            ExportColumn::make('user.name'),
            ExportColumn::make('fromCurrency.code')
                ->label('From Currency'),
            ExportColumn::make('toCurrency.code')
                ->label('To Currency'),
            ExportColumn::make('amount_from')
                ->label('Amount From'),
            ExportColumn::make('amount_to')
                ->label('Amount To'),
            ExportColumn::make('rate')
                ->label('Exchange Rate'),
            ExportColumn::make('status')
                ->formatStateUsing(
                    fn($state) => ($state instanceof ExchangeOrderStatus ? $state : ExchangeOrderStatus::from((int) $state))->label()
                ),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $user = $export->user;
        app()->setLocale($user->locale ?? config('app.locale'));

        $body = __('exchange.export_completed') . ' ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(['user', 'fromCurrency', 'toCurrency']);
    }
} 
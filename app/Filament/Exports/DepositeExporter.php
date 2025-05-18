<?php

namespace App\Filament\Exports;

use App\Models\Deposit;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\DepositStatus;
use Filament\Notifications\Notification;
class DepositeExporter extends Exporter
{
    protected static ?string $model = Deposit::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('order_number'),
            ExportColumn::make('user.name'),
            ExportColumn::make('currencyCode.code'),
            ExportColumn::make('amount'),
            ExportColumn::make('status')
                ->formatStateUsing(
                    fn($state) => ($state instanceof DepositStatus ? $state : DepositStatus::from((int) $state))->label()
                ),
            ExportColumn::make('created_at'),
            //
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        //切換語系為用語系為
        $user = $export->user;
        app()->setLocale($user->locale ?? config('app.locale'));

        $body = __('deposit.export_completed') . ' ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(['user', 'currencyCode']);
    }
}

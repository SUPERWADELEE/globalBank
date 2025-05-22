<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExchangeLogResource\Pages;
use App\Filament\Resources\ExchangeLogResource\RelationManagers;
use App\Models\ExchangeLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\ExchangeOrder;
use Filament\Tables\Columns\TextColumn;
use App\Enums\ExchangeOrderStatus;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use App\Models\CurrencyCode;
use Filament\Tables\Enums\FiltersLayout;

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
                TextColumn::make('user.name')->label(__('exchange.user')),
                TextColumn::make('buy_display')
                    ->label('買入')
                    ->getStateUsing(function ($record) {
                        return number_format($record->amount_to, 2) . ' ' . ($record->toCurrency->code ?? '');
                    }),
                TextColumn::make('sell_display')
                    ->label('賣出')
                    ->getStateUsing(function ($record) {
                        return '-' . number_format($record->amount_from, 2) . ' ' . ($record->fromCurrency->code ?? '');
                    }),
                TextColumn::make('unit_price')
                    ->label('單位價格')
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
                    ->options(CurrencyCode::pluck('code', 'id')),
                SelectFilter::make('to_currency_id')
                    ->label(__('exchange.sell_currency_code'))
                    ->options(CurrencyCode::pluck('code', 'id')),
                static::makeDateRangeFilter(),
                static::makeQuickRangeFilter(),
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
    /**
     * 日期範圍 Filter：開始／結束日互斥檢查 + 查詢
     */
    protected static function makeDateRangeFilter(): Filter
    {
        return Filter::make('created_at_range')
            ->label(__('user.register_time'))
            ->form([
                DatePicker::make('from')
                    ->label(__('user.start_date'))
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if ($state && $get('until') && $state > $get('until')) {
                            $set('from', null);
                            Notification::make()
                                ->title(__('user.error.start_after_end')) // 建議把訊息也抽翻譯
                                ->danger()
                                ->send();
                        }
                    }),

                DatePicker::make('until')
                    ->label(__('user.end_date'))
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if ($state && $get('from') && $state < $get('from')) {
                            $set('until', null);
                            Notification::make()
                                ->title(__('user.error.end_before_start'))
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->query(function ($query, array $data) {
                return $query
                    ->when($data['from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                    ->when($data['until'], fn($q, $date) => $q->whereDate('created_at', '<=', $date));
            });
    }

    /**
     * 快捷範圍 Filter：今日／昨日／近幾日／本週／本月
     */
    protected static function makeQuickRangeFilter(): Filter
    {
        return Filter::make('quick_range')
            ->label(__('user.quick_range'))
            ->form([
                Select::make('preset')
                    ->label(__('user.date_range'))
                    ->options([
                        'today'      => __('user.range.today'),
                        'yesterday'  => __('user.range.yesterday'),
                        'last7'      => __('user.range.last7'),
                        'last30'     => __('user.range.last30'),
                        'this_week'  => __('user.range.this_week'),
                        'last_week'  => __('user.range.last_week'),
                        'this_month' => __('user.range.this_month'),
                    ])
                    ->placeholder(__('user.range.select')),
            ])
            ->query(function ($query, array $data) {
                if (blank($data['preset'])) {
                    return $query;
                }

                return match ($data['preset']) {
                    'today'      => $query->whereDate('created_at', Carbon::today()),
                    'yesterday'  => $query->whereDate('created_at', Carbon::yesterday()),
                    'last7'      => $query->whereDate('created_at', '>=', Carbon::today()->subDays(6)),
                    'last30'     => $query->whereDate('created_at', '>=', Carbon::today()->subDays(29)),
                    'this_week'  => $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
                    'last_week'  => $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]),
                    'this_month' => $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]),
                    default      => $query,
                };
            });
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepositLogResource\Pages;
use App\Models\Deposit;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Enums\DepositStatus;
use Filament\Tables\Filters\SelectFilter;
use App\Models\User;
use App\Models\CurrencyCode;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use App\Filament\Exports\DepositeExporter;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use App\Models\DepositLog;
use App\Filament\Filters\CommonFilters;


class DepositLogResource extends Resource
{
    protected static ?string $model = DepositLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    public static function getNavigationGroup(): ?string
    {
        return __('transaction.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('deposit.title');
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
            ->columns([
                TextColumn::make('order_number')
                    ->label(__('deposit.order_number')),
                TextColumn::make('user.username')
                    ->label(__('user.username')),
                TextColumn::make('currencyCode.code')
                    ->label(__('deposit.currency_code')),
                TextColumn::make('amount')
                    ->label(__('deposit.amount')),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state instanceof DepositStatus ? $state : DepositStatus::from((int) $state)) {
                        DepositStatus::Pending => 'warning',
                        DepositStatus::Success => 'success',
                        DepositStatus::Failed => 'danger',
                    })
                    ->formatStateUsing(fn($state) => ($state instanceof DepositStatus ? $state : DepositStatus::from((int) $state))->label())
                    ->label(__('deposit.status.status')),
                TextColumn::make('created_at')
                    ->label(__('deposit.created_at')),
            ])
            ->filters([
                CommonFilters::relationTextLike(
                    'user',
                    'username',
                    'user_username',
                    __('user.username'),
                    __('common.placeholder')
                ),

                // 2. 狀態篩選：從 Enum 取出 value => label
                SelectFilter::make('status')
                    ->label(__('deposit.status.status'))
                    ->options(
                        collect(DepositStatus::cases())
                            ->mapWithKeys(fn(DepositStatus $case) => [
                                $case->value => $case->label(),
                            ])
                            ->toArray()
                    )
                    ->native(false),
                SelectFilter::make('currency_code_id')
                    ->options(CurrencyCode::all()->pluck('code', 'id'))
                    ->label(__('deposit.currency_code'))
                    ->native(false),
                SelectFilter::make('order_number')
                    ->options(Deposit::where('status', '1')->pluck('order_number', 'order_number'))
                    ->label(__('deposit.order_number'))
                    ->native(false),
                static::makeDateRangeFilter(),
                static::makeQuickRangeFilter(),
            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                ExportAction::make()
                    ->exporter(DepositeExporter::class)
                    ->modalHeading(__('deposit.export_heading'))
                    ->modalDescription(__('deposit.export_description'))
                    ->label(__('common.export')),
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
            'index' => Pages\ListDepositLogs::route('/'),
            'create' => Pages\CreateDepositLog::route('/create'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->whereIn('status', [DepositStatus::Success, DepositStatus::Failed]);
    // }
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

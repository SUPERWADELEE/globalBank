<?php
namespace App\Filament\Filters;

use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

class CommonDateFilters
{
    /**
     * 日期範圍 Filter
     */
    public static function dateRange(
        string $field = 'created_at',
        ?string $label = null,
        ?string $fromLabel = null,
        ?string $untilLabel = null
    ): Filter {
        return Filter::make("{$field}_range")
            ->label($label ?? __('user.register_time'))
            ->form([
                DatePicker::make('from')
                    ->native(false)
                    ->displayFormat('Y-m-d')
                    ->label($fromLabel ?? __('user.start_date'))
                    ->reactive()
                    ->locale('en')
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if ($state && $get('until') && $state > $get('until')) {
                            $set('from', null);
                            Notification::make()
                                ->title(__('user.error.start_after_end'))
                                ->danger()
                                ->send();
                        }
                    }),

                DatePicker::make('until')
                    ->native(false)
                    ->displayFormat('Y-m-d')
                    ->label($untilLabel ?? __('user.end_date'))
                    ->reactive()
                    ->locale('en') 
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
            ->query(function ($query, array $data) use ($field) {
                return $query
                    ->when($data['from'], fn($q, $date) => $q->whereDate($field, '>=', $date))
                    ->when($data['until'], fn($q, $date) => $q->whereDate($field, '<=', $date));
            });
    }

    /**
     * 快捷範圍 Filter
     */
    public static function quickRange(
        string $field = 'created_at',
        ?string $label = null
    ): Filter {
        return Filter::make("quick_{$field}_range")
            ->label($label ?? __('user.quick_range'))
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
                    ->placeholder(__('user.range.select'))
                    ->native(false),
            ])
            ->query(function ($query, array $data) use ($field) {
                if (blank($data['preset'])) {
                    return $query;
                }
                return match ($data['preset']) {
                    'today'      => $query->whereDate($field, Carbon::today()),
                    'yesterday'  => $query->whereDate($field, Carbon::yesterday()),
                    'last7'      => $query->whereDate($field, '>=', Carbon::today()->subDays(6)),
                    'last30'     => $query->whereDate($field, '>=', Carbon::today()->subDays(29)),
                    'this_week'  => $query->whereBetween($field, [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
                    'last_week'  => $query->whereBetween($field, [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]),
                    'this_month' => $query->whereBetween($field, [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]),
                    default      => $query,
                };
            });
    }
}
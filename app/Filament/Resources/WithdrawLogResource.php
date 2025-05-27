<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawLogResource\Pages;
use App\Models\Withdraw;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Actions\ExportAction;
use App\Enums\WithdrawStatus;
use App\Models\CurrencyCode;
use App\Filament\Exports\WithdrawExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use App\Models\WithdrawLog;
use App\Filament\Filters\CommonDateFilters;
use App\Filament\Filters\CommonFilters;

class WithdrawLogResource extends Resource
{
    protected static ?string $model = WithdrawLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    public static function getNavigationGroup(): ?string
    {
        return __('transaction.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('withdraw.title');
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
                    ->label(__('withdraw.order_number')),
                TextColumn::make('user.username')
                    ->label(__('user.username')),
                TextColumn::make('currencyCode.code')
                    ->label(__('withdraw.currency_code')),
                TextColumn::make('amount')
                    ->label(__('withdraw.amount')),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state instanceof WithdrawStatus ? $state : WithdrawStatus::from((int) $state)) {
                        WithdrawStatus::Pending => 'warning',
                        WithdrawStatus::Success => 'success',
                        WithdrawStatus::Failed => 'danger',
                    })
                    ->formatStateUsing(fn($state) => ($state instanceof WithdrawStatus ? $state : WithdrawStatus::from((int) $state))->label())
                    ->label(__('withdraw.status')),
                TextColumn::make('created_at')
                    ->label(__('withdraw.created_at')),
            ])
            ->filters([
                CommonFilters::relationTextLike(
                    'user',
                    'username',
                    'user_username',
                    __('user.username'),
                    __('common.placeholder')
                ),

                SelectFilter::make('status')
                    ->label(__('withdraw.status'))
                    ->options(
                        collect(WithdrawStatus::cases())
                            ->mapWithKeys(fn(WithdrawStatus $case) => [
                                $case->value => $case->label(),
                            ])
                            ->toArray()
                    )
                    ->searchable(),

                SelectFilter::make('currency_code_id')
                    ->options(CurrencyCode::all()->pluck('code', 'id'))
                    ->label(__('withdraw.currency_code'))
                    ->searchable(),
                CommonFilters::textLike('order_number', __('withdraw.order_number'), __('common.placeholder')),
                CommonDateFilters::dateRange(),
                CommonDateFilters::quickRange(),
            ], layout: FiltersLayout::AboveContent)
            ->headerActions([
                ExportAction::make()
                    ->exporter(WithdrawExporter::class)
                    ->modalHeading(__('withdraw.export_heading'))
                    ->modalDescription(__('withdraw.export_description'))
                    ->label(__('common.export'))
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
            'index' => Pages\ListWithdrawsLog::route('/'),
            'create' => Pages\CreateWithdrawLog::route('/create'),
        ];
    }
    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->whereIn('status', [WithdrawStatus::Success, WithdrawStatus::Failed]);
    // }

}

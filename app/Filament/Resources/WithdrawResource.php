<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawResource\Pages;
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


class WithdrawResource extends Resource
{
    protected static ?string $model = Withdraw::class;

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
                TextColumn::make('user.name')
                    ->label(__('withdraw.user')),
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
                SelectFilter::make('user_id')
                    ->options(
                        Withdraw::with('user')
                            ->get()
                            ->pluck('user.username', 'user.id')
                            ->unique()
                    )
                    ->label(__('withdraw.user'))
                    ->native(false)
                    ->searchable(),
                SelectFilter::make('currency_code_id')
                    ->options(CurrencyCode::all()->pluck('code', 'id'))
                    ->label(__('withdraw.currency_code'))
                    ->native(false)
                    ->searchable(),
                SelectFilter::make('order_number')
                    ->options(Withdraw::where('status', '1')->pluck('order_number', 'order_number'))
                    ->label(__('withdraw.order_number'))
                    ->native(false)
                    ->searchable(),
                // 4. 建立時間：改為範圍選擇
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('起始日'),
                        DatePicker::make('until')->label('結束日'),
                    ])
                    ->query(
                        fn($query, $data) => $query
                            ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('created_at', '<=', $data['until']))
                    )
                    ->label(__('withdraw.created_at')),
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
            'index' => Pages\ListWithdraws::route('/'),
            'create' => Pages\CreateWithdraw::route('/create'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('status', [WithdrawStatus::Success, WithdrawStatus::Failed]);
    }
}

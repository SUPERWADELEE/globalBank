<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepositOrderResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Models\Deposit;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use App\Models\CurrencyCode;
use App\Enums\DepositStatus;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;

class DepositOrderResource extends Resource
{
    protected static ?string $model = Deposit::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    public static function getNavigationGroup(): ?string
    {
        return __('common.order_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('deposit.order.title');
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
                TextColumn::make('user.name')
                    ->label(__('deposit.user')),
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
                TextColumn::make('deposit_address')
                    ->label(__('deposit.deposit_address')),
                TextColumn::make('tx_hash')
                    ->label(__('common.tx_hash')),
            ])
            ->filters([

                SelectFilter::make('currency_code_id')
                    ->options(CurrencyCode::all()->pluck('code', 'id'))
                    ->label(__('deposit.currency_code'))
                    ->native(false)
                    ->searchable(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Action::make('markAsCompleted')
                    ->label(__('common.mark_as_completed'))
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn($record) => $record->update(['status' => \App\Enums\DepositStatus::Success])),
                Action::make('markAsFailed')
                    ->label(__('common.mark_as_failed'))
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->action(fn($record) => $record->update(['status' => \App\Enums\DepositStatus::Failed])),
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
            'index' => Pages\ListDepositOrders::route('/'),
            'create' => Pages\CreateDepositOrder::route('/create'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', DepositStatus::Pending);
    }
}

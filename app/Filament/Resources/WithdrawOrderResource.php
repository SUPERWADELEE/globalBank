<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawOrderResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Models\Withdraw;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use App\Models\CurrencyCode;
use Filament\Forms\Components\DatePicker;
use App\Enums\WithdrawStatus;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\EditAction;
use App\Models\WithdrawOrder;

class WithdrawOrderResource extends Resource
{
    protected static ?string $model = WithdrawOrder::class;
    protected static ?string $modelLabel       = '出金訂單';
    protected static ?string $pluralModelLabel = '出金訂單列表';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    public static function getNavigationGroup(): ?string
    {
        return __('common.order_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('withdraw.order.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('tx_hash')
                    ->label(__('common.tx_hash'))
                    ->required()
                    ->maxLength(255),
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
                TextColumn::make('withdraw_address')
                    ->label(__('withdraw.withdraw_address')),
                TextColumn::make('tx_hash')
                    ->label(__('common.tx_hash')),
            ])
            ->filters([

                SelectFilter::make('currency_code_id')
                    ->options(CurrencyCode::all()->pluck('code', 'id'))
                    ->label(__('withdraw.currency_code'))
                    ->native(false)
                    ->searchable(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                EditAction::make()
                    ->label(
                        fn($record) =>
                        blank($record->tx_hash)
                            ? __('withdraw.add_tx_hash')
                            : __('withdraw.modify_tx_hash')
                    ),
                Action::make('markAsCompleted')
                    ->label(__('common.mark_as_completed'))
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn($record) => filled($record->tx_hash))
                    ->action(fn($record) => $record->update(['status' => \App\Enums\WithdrawStatus::Success])),

                Action::make('markAsFailed')
                    ->label(__('common.mark_as_failed'))
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->action(fn($record) => $record->update(['status' => \App\Enums\WithdrawStatus::Failed])),
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
            'index' => Pages\ListWithdrawOrders::route('/'),
            'create' => Pages\CreateWithdrawOrder::route('/create'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', WithdrawStatus::Pending);
    }
}

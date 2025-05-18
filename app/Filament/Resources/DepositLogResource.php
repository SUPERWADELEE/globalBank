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

class DepositLogResource extends Resource
{
    protected static ?string $model = Deposit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('transaction.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('deposite.title');
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
                    ->label(__('deposite.order_number')),
                TextColumn::make('user.name')
                    ->label(__('deposite.user')),
                TextColumn::make('currencyCode.code')
                    ->label(__('deposite.currency_code')),
                TextColumn::make('amount')
                    ->label(__('deposite.amount')),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state instanceof DepositStatus ? $state : DepositStatus::from((int) $state)) {
                        DepositStatus::Pending => 'warning',
                        DepositStatus::Success => 'success',
                        DepositStatus::Failed => 'danger',
                    })
                    ->formatStateUsing(fn($state) => ($state instanceof DepositStatus ? $state : DepositStatus::from((int) $state))->label()),
                TextColumn::make('created_at')
                    ->label(__('deposite.created_at')),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->options(
                        Deposit::with('user')
                            ->get()
                            ->pluck('user.username', 'user.id')
                            ->unique()
                    )
                    ->label(__('deposite.user'))
                    ->native(false)
                    ->searchable(),
                SelectFilter::make('currency_code_id')
                    ->options(CurrencyCode::all()->pluck('code', 'id'))
                    ->label(__('deposite.currency_code'))
                    ->native(false)
                    ->searchable(),
                SelectFilter::make('order_number')
                    ->options(Deposit::where('status', '1')->pluck('order_number', 'order_number'))
                    ->label(__('deposite.order_number'))
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
                    ->label(__('deposite.created_at')),
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
            'index' => Pages\ListDepositLogs::route('/'),
            'create' => Pages\CreateDepositLog::route('/create'),
            // 'edit' => Pages\EditDepositLog::route('/{record}/edit'),
        ];
    }
}

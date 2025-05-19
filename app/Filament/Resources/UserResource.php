<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Enums\FiltersLayout;
use App\Enums\UserStatusEnum;
use App\Models\UserLevel;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    public static function getNavigationLabel(): string
    {
        return __('user.user_management');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('username')
                    ->required()
                    ->maxLength(255),
                TextInput::make('register_location')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->options(collect(UserStatusEnum::cases())->mapWithKeys(fn($case) => [
                        $case->value => $case->getLabel()
                    ]))
                    ->default(UserStatusEnum::Active->value),
                Select::make('user_level_id')
                    ->options(UserLevel::pluck('name', 'id')->toArray())

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('user.name')),
                Tables\Columns\TextColumn::make('email')->label(__('user.email')),
                Tables\Columns\TextColumn::make('username')->label(__('user.username')),
                Tables\Columns\TextColumn::make('userLevel.name')->label(__('user.level')),
                Tables\Columns\TextColumn::make('register_location')->label(__('user.register_location')),
                Tables\Columns\TextColumn::make('phone')->label(__('user.phone')),
                Tables\Columns\TextColumn::make('status')->label(__('user.status')),


                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('user.register_time'))
                    ->dateTime('Y-m-d H:i:s'),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('created_at')
                    ->label(__('user.register_time'))
                    ->options(User::pluck('created_at', 'created_at')->toArray()),
                Tables\Filters\SelectFilter::make('name')
                    ->label(__('user.name'))
                    ->options(User::pluck('name', 'name')->toArray()),
                Tables\Filters\SelectFilter::make('email')
                    ->label(__('user.email'))
                    ->options(User::pluck('email', 'email')->toArray()),
                Tables\Filters\SelectFilter::make('username')
                    ->label(__('user.username'))
                    ->options(User::pluck('username', 'username')->toArray()),
                Tables\Filters\SelectFilter::make('register_location')
                    ->label(__('user.register_location'))
                    ->options(User::pluck('register_location', 'register_location')->toArray()),
                Tables\Filters\SelectFilter::make('phone')
                    ->label(__('user.phone'))
                    ->options(User::pluck('phone', 'phone')->toArray()),

                //
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('wallet')
                    ->label(__('user.account_operation'))
                    ->url(fn(User $record) => UserResource::getUrl('user-wallet-page', ['record' => $record->id]))
                    ->icon('heroicon-o-wallet'),
                Tables\Actions\Action::make('operation_log')
                    ->label(__('user.operation_log'))
                    ->url(fn(User $record) => UserResource::getUrl('user-wallet-logs', ['record' => $record->id]))
                    ->icon('heroicon-o-clock'),
                // Tables\Actions\Action::make('operation_log')
                //     ->label(__('user.operation_log'))
                //     ->url(fn(User $record) => UserResource::getUrl('user-operation-log-page', ['record' => $record->id]))
                //     ->icon('heroicon-o-clock'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'user-wallet-page' => Pages\UserWalletPage::route('/{record}/wallets'),
            'user-wallet-logs' => Pages\UserWalletLogPage::route('/{record}/wallets/logs'),

            // 'financial-operation' => Pages\FinancialOperation::route('/{record}/financial-operation'),
            // 'wallet' => WalletResource::route('/{record}/wallet'),

        ];
    }
}

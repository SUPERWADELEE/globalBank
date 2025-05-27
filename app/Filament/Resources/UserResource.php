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
use App\Models\UserLevel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Set;
use App\Models\AdminUserTeam;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action as TableAction;
use App\Filament\Filters\CommonFilters;
use App\Filament\Filters\CommonDateFilters;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Component;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    public static function getNavigationLabel(): string
    {
        return __('user.user_management');
    }
    public static function getNavigationGroup(): string
    {
        return __('user.user_management');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->maxLength(255)
                    ->label(__('user.name'))
                    ->rules(['required'])
                    ->markAsRequired(),
                TextInput::make('email')
                    ->rules(['required', 'email']) 
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label(__('user.email'))
                    ->markAsRequired(),
                TextInput::make('username')
                    ->rules(['required'])
                    ->maxLength(255)
                    ->label(__('user.username'))
                    ->unique(ignoreRecord: true)
                    ->markAsRequired(),
                Select::make('register_location')
                    ->label(__('user.register_location'))
                    ->options(AdminUserTeam::pluck('name', 'name')->toArray())
                    ->searchable()     
                    ->label(__('user.register_location'))
                    ->rules(['required'])
                    ->markAsRequired(),
                TextInput::make('phone')
                    ->rules(['required'])
                    ->maxLength(255)
                    ->label(__('user.phone'))
                    ->tel()
                    ->markAsRequired(),
                TextInput::make('password')
                    ->label(__('user.password'))
                    ->password()
                    ->rules(
                        fn (Component $component): array => [
                            $component->getLivewire()->record === null
                                ? 'required'             
                                : 'nullable',            
                        ]
                    )
                    ->revealable()
                    ->suffixAction(
                        Action::make('generatePassword')
                            ->tooltip(__('user.random_password'))    
                            ->icon('heroicon-o-sparkles')             
                            ->color('secondary')                       
                            ->action(
                                fn(Set $set) =>
                                $set('password', Str::random(12))      
                            )
                    )
                    ->default(Str::random(12))
                    ->markAsRequired()
                    ->maxLength(255),

                Select::make('user_level_id')
                    ->rules(['required'])
                    ->options(UserLevel::pluck('name', 'id')->toArray())
                    ->label(__('user.level'))
                    ->markAsRequired(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')->label(__('user.username')),
                Tables\Columns\TextColumn::make('email')->label(__('user.email')),
                Tables\Columns\TextColumn::make('username')->label(__('user.username')),
                Tables\Columns\TextColumn::make('userLevel.name')->label(__('user.level')),
                Tables\Columns\TextColumn::make('register_location')->label(__('user.register_location')),
                Tables\Columns\TextColumn::make('phone')->label(__('user.phone')),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('user.status'))
                    ->formatStateUsing(function ($state) {
                        return $state == 1 ? __('user.status_enabled') : __('user.status_frozen');
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('user.register_time'))
                    ->dateTime('Y-m-d H:i:s'),
            ])
            ->filters([
                CommonFilters::textLike('username', __('user.username'), __('common.placeholder')),
                CommonFilters::textLike('email', __('user.email'), __('common.placeholder')),
                Tables\Filters\SelectFilter::make('register_location')
                    ->label(__('user.register_location'))
                    ->options(User::pluck('register_location', 'register_location')->toArray()),
                Tables\Filters\SelectFilter::make('user_level_id')
                    ->label(__('user.level'))
                    ->options(UserLevel::pluck('name', 'id')->toArray()),
                CommonDateFilters::dateRange(),
                CommonDateFilters::quickRange(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('show_qr')
                    ->label('顯示 QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading('TOTP QR Code')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('關閉')
                    ->visible(fn(User $record) => Auth::user()->can('view_user_qr_code'))
                    ->modalContent(function ($record) {
                        if (!$record->otp_secret) {
                            return view('components.simple-text', ['text' => __('user.no_otp_secret')]);
                        }
                        $tfa = new \RobThree\Auth\TwoFactorAuth(new \RobThree\Auth\Providers\Qr\EndroidQrCodeProvider());
                        $label = 'Global Exchange:' . $record->username;
                        $qr = $tfa->getQRCodeImageAsDataUri($label, $record->otp_secret);
                        return view('components.qr-code-display', [
                            'qrCode' => $qr,
                            'email' => $record->email,
                            'secret' => $record->otp_secret,
                        ]);
                    }),
                Tables\Actions\Action::make('wallet')
                    ->label(__('user.account_operation'))
                    ->url(fn(User $record) => UserResource::getUrl('user-wallet-page', ['record' => $record->id]))
                    ->icon('heroicon-o-wallet')
                    ->visible(fn(User $record) => Auth::user()->can('view_user_wallet', $record)),
                Tables\Actions\Action::make('operation_log')
                    ->label(__('user.operation_log'))
                    ->url(fn(User $record) => UserResource::getUrl('user-wallet-logs', ['record' => $record->id]))
                    ->icon('heroicon-o-clock')
                    ->visible(fn(User $record) => Auth::user()->can('view_user_wallet_logs', $record)),
                TableAction::make('toggleStatus')
                    ->label(fn(User $record) => $record->status ? '凍結' : '解凍')
                    ->color(fn(User $record) => $record->status ? 'danger' : 'success')
                    ->icon(fn(User $record) => $record->status ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                    ->requiresConfirmation()
                    ->action(function (User $record, $livewire) {
                        $newStatus = $record->status ? 0 : 1;  // true → 0, false → 1
                        $record->update(['status' => $newStatus]);

                        Notification::make()
                            ->title($newStatus ? '已解凍' : '已凍結')
                            ->success()
                            ->send();

                        $livewire->dispatch('refresh');
                    })

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
        ];
    }
}

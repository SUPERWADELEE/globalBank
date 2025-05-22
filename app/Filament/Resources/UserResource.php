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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Set;
use App\Models\AdminUserTeam;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;

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
                    ->required()
                    ->maxLength(255)
                    ->label(__('user.name')),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label(__('user.email')),
                TextInput::make('username')
                    ->required()
                    ->maxLength(255)
                    ->label(__('user.username')),
                Select::make('register_location')
                    ->label(__('user.register_location'))
                    ->options(AdminUserTeam::pluck('name', 'name')->toArray())
                    ->searchable()     // 可搜尋
                    ->required()
                    ->label(__('user.register_location')),
                TextInput::make('phone')
                    ->required()
                    ->maxLength(255)
                    ->label(__('user.phone')),
                TextInput::make('password')
                    ->label(__('user.password'))
                    ->password()
                    // 建立時必填；編輯時可空白（表示不變更密碼）
                    ->required(fn($livewire) => $livewire->record === null)
                    ->revealable()
                    // 右側按鈕：產生隨機密碼
                    ->suffixAction(
                        Action::make('generatePassword')
                            ->tooltip(__('user.random_password'))      // 滑鼠提示
                            ->icon('heroicon-o-sparkles')              // 圖示可換
                            ->color('secondary')                       // 按鈕顏色
                            ->action(
                                fn(Set $set) =>
                                $set('password', Str::random(12))      // 寫回欄位
                            )
                    )
                    ->default(Str::random(12))
                    ->maxLength(255),

                Select::make('user_level_id')
                    ->options(UserLevel::pluck('name', 'id')->toArray())
                    ->label(__('user.level')),

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
                Tables\Columns\TextColumn::make('status')
                    ->label(__('user.status'))
                    ->formatStateUsing(function ($state) {
                        return $state == 1 ? __('user.status_enabled') : __('user.status_frozen');
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('user.register_time'))
                    ->dateTime('Y-m-d H:i:s'),

                // Tables\Columns\TextColumn::make('created_at')
                // ->label(__('user.register_time'))
                // ->formatStateUsing(function ($state) {
                //     $locale = Auth::user()?->locale ?? 'zh_TW'; // 預設 zh_TW
                //     $timezone = match ($locale) {
                //         'zh_TW' => 'Asia/Taipei',
                //         'ja'    => 'Asia/Tokyo',
                //         'en_US' => 'UTC',
                //         default => 'UTC',
                //     };
            
                //     return Carbon::parse($state)->setTimezone($timezone)->format('Y-m-d H:i:s');
                // }),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('email')
                    ->label(__('user.email'))
                    ->options(User::pluck('email', 'email')->toArray()),
                Tables\Filters\SelectFilter::make('username')
                    ->label(__('user.username'))
                    ->options(User::pluck('username', 'username')->toArray()),
                Tables\Filters\SelectFilter::make('register_location')
                    ->label(__('user.register_location'))
                    ->options(User::pluck('register_location', 'register_location')->toArray()),
                Tables\Filters\SelectFilter::make('user_level_id')
                    ->label(__('user.level'))
                    ->options(UserLevel::pluck('name', 'id')->toArray()),
                Tables\Filters\SelectFilter::make('phone')
                    ->label(__('user.phone'))
                    ->options(User::pluck('phone', 'phone')->toArray()),
                Filter::make('created_at_range')
                    ->label(__('user.register_time'))
                    ->form([
                        DatePicker::make('from')
                            ->label(__('user.start_date'))
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // 若起始日 > 終止日，重設並警示
                                if ($state && $get('until') && $state > $get('until')) {
                                    $set('from', null);
                                    Notification::make()
                                        ->title('開始日不可大於結束日')
                                        ->danger()
                                        ->send();
                                }
                            }),

                        DatePicker::make('until')
                            ->label(__('user.end_date'))
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // 若終止日 < 起始日，重設並警示
                                if ($state && $get('from') && $state < $get('from')) {
                                    $set('until', null);
                                    Notification::make()
                                        ->title('結束日不可小於開始日')
                                        ->danger()
                                        ->send();
                                }
                            }),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn($q, $date) => $q->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['until'],
                                fn($q, $date) => $q->whereDate('created_at', '<=', $date)
                            );
                    }),
                Filter::make('quick_range')
                    ->label(__('user.quick_range'))
                    ->form([
                        Select::make('preset')
                            ->label(__('user.date_range'))
                            ->options([
                                'today'      => '今日',
                                'yesterday'  => '昨日',
                                'last7'      => '近 7 日',
                                'last30'     => '近 30 日',
                                'this_week'  => '本週',
                                'last_week'  => '上週',
                                'this_month' => '本月',
                            ])
                            ->placeholder('請選擇…'),
                    ])
                    ->query(function ($query, array $data) {
                        if (blank($data['preset'])) {
                            return $query;               // 沒選就不過濾
                        }

                        switch ($data['preset']) {
                            case 'today':
                                return $query->whereDate('created_at', Carbon::today());

                            case 'yesterday':
                                return $query->whereDate('created_at', Carbon::yesterday());

                            case 'last7':
                                return $query->whereDate('created_at', '>=', Carbon::today()->subDays(6));

                            case 'last30':
                                return $query->whereDate('created_at', '>=', Carbon::today()->subDays(29));

                            case 'this_week':
                                return $query->whereDate('created_at', '>=', Carbon::now()->startOfWeek())
                                    ->whereDate('created_at', '<=', Carbon::now()->endOfWeek());

                            case 'last_week':
                                return $query->whereDate('created_at', '>=', Carbon::now()->subWeek()->startOfWeek())
                                    ->whereDate('created_at', '<=', Carbon::now()->subWeek()->endOfWeek());

                            case 'this_month':
                                return $query->whereDate('created_at', '>=', Carbon::now()->startOfMonth())
                                    ->whereDate('created_at', '<=', Carbon::now()->endOfMonth());
                        }

                        return $query;
                    }),

                //
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
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

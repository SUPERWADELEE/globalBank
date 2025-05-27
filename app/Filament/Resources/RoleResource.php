<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Spatie\Permission\Models\Role;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\CheckboxList;
use Spatie\Permission\Models\Permission;
use App\Filament\Filters\CommonFilters;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $modelLabel = 'Role';

    protected static ?string $navigationIcon = 'heroicon-o-key';

    public static function getNavigationGroup(): ?string
    {
        return __('system_management.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_user.navigation.role_management');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('群組名稱')
                    ->unique(ignoreRecord: true)
                    ->required(),
                Section::make('權限分配')->schema([
                    Grid::make(2)->schema([
                       
                        Fieldset::make(__('system_management.title'))->schema([
                             // 白名單
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        includeAndPatterns: ['admin::ip::white::list'],
                                        excludePatterns: [
                                            'view_admin::ip::white::list',
                                            'update_admin::ip::white::list',
                                        ]
                                    )
                                )
                                ->bulkToggleable()
                                ->label(__('permissions.groups.whitelist')),

                            // 管理員帳號
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        endsWithPatterns: ['admin::user'],
                                        excludePatterns: [
                                            'view_admin::user',
                                            'delete_admin::user',
                                        ]
                                    )
                                )
                                ->bulkToggleable()
                                ->label(__('permissions.groups.admin_user')),

                            // Role 群組
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        includeAndPatterns: ['role'],
                                        excludePatterns: [
                                            'view_role',
                                            'delete_role',
                                        ]
                                    )
                                )
                                ->bulkToggleable()
                                ->label(__('role.title')),

                            // Admin User Team
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        includeAndPatterns: ['team'],
                                        excludePatterns: [
                                            'view_admin::user::team',
                                            'update_admin::user::team',
                                        ]
                                    )
                                )
                                ->bulkToggleable()
                                ->label(__('admin_user.user_team.title')),
                        ]),



                        // 訂單管理
                        Fieldset::make(__('common.order_management'))->schema([
                            // 入金訂單
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        includeAndPatterns: ['view_any_deposit::order']
                                    )
                                )
                                ->label(__('deposit.order.title'))
                                ->bulkToggleable(),

                            // 出金訂單
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        includeAndPatterns: ['view_any_withdraw::order']
                                    )
                                )
                                ->label(__('withdraw.order.title'))
                                ->bulkToggleable(),
                        ]),

                        // 匯率群組
                        Fieldset::make(__('permissions.groups.rate'))->schema([
                            CheckboxList::make('permissions')
                                ->label(__('permissions.groups.rate'))
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        includeOrPatterns: ['view_any_rate', 'edit_'],
                                        includeAndPatterns: ['rate'],
                                        excludePatterns: ['rate_rate', 'edit_rate']

                                    )
                                )
                                ->bulkToggleable(),
                        ]),

                        // 使用者管理
                        Fieldset::make(__('user.user_management'))->schema([
                            CheckboxList::make('permissions')
                                ->label(__('user.user_management'))
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        endsWithPatterns: ['_user'],
                                        excludePatterns: ['view_any_user', 'delete_user', 'admin::user']
                                    )
                                )
                                ->bulkToggleable(),

                            // 帳戶操作
                            CheckboxList::make('permissions')
                                ->label(__('user.account_operation'))
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        endsWithPatterns: ['user_wallet']
                                    )
                                )
                                ->bulkToggleable(),

                            // 操作日誌
                            CheckboxList::make('permissions')
                                ->label(__('user.operation_log'))
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        includeAndPatterns: ['user_wallet_logs']
                                    )
                                )
                                ->bulkToggleable(),
                            // qr code
                            CheckboxList::make('permissions')
                                ->label(__('user.qr_code'))
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        endsWithPatterns: ['qr_code']
                                    )
                                )
                                ->bulkToggleable(),
                        ]),

                        // 平台錢包
                        Fieldset::make(__('platform_wallet.title.platform_wallet'))->schema([
                            CheckboxList::make('permissions')
                                ->label(__('platform_wallet.title.platform_wallet'))
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        includeAndPatterns: ['view_any_platform::wallet']
                                    )
                                )
                                ->bulkToggleable(),
                        ]),

                        // 管理員帳號設定
                        Fieldset::make(__('admin_user.account_settings'))->schema([
                            CheckboxList::make('permissions')
                                ->label(__('admin_user.account_settings'))
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        includeAndPatterns: ['account_settings']
                                    )
                                )
                                ->bulkToggleable(),
                        ]),
                        Fieldset::make(__('deposit_location.title'))->schema([
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        includeOrPatterns: ['view_any_deposit::location', 'create_deposit::location', 'delete_deposit::location']
                                    )
                                )
                                ->bulkToggleable()
                                ->label(__('deposit_location.title')),
                            CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->columns(4)
                            ->options(
                                self::getDbFilteredPermissions(
                                    includeOrPatterns: ['view_any_fee', 'update_fee']
                                )
                            )
                            ->bulkToggleable()
                            ->label(__('usdt_setting.fee.title')),
                        ]),
                        // 交易紀錄
                        Fieldset::make(__('record.title'))->schema([
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        includeAndPatterns: ['view_any_deposit::log']
                                    )
                                )
                                ->bulkToggleable()
                                ->label(__('deposit.title')),
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        includeAndPatterns: ['view_any_withdraw'],
                                        excludePatterns: ['view_any_withdraw::order']
                                    )
                                )
                                ->bulkToggleable()
                                ->label(__('withdraw.title')),
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    self::getDbFilteredPermissions(
                                        includeAndPatterns: ['view_any_exchange::log']
                                    )
                                )
                                ->bulkToggleable()
                                ->label(__('exchange.title')),
                        ])->label(__('permissions.records')),
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('role.role_name')),
                Tables\Columns\TextColumn::make('users.name')
                    ->label(__('admin_user.admin_user')),
            ])
            ->filters([
                CommonFilters::textLike('name', __('role.role_name'), __('common.placeholder')),
                CommonFilters::relationTextLike(
                    'users',
                    'name',
                    'user_name',
                    __('admin_user.name'),
                    __('common.placeholder')
                ),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
    public static function getDbFilteredPermissions(
        array $includeAndPatterns  = [],
        array $includeOrPatterns   = [],
        array $excludePatterns     = [],
        array $endsWithPatterns    = []
    ): array {
        $query = Permission::query();

        foreach ($endsWithPatterns as $pattern) {
            $query->where('name', 'like', "%{$pattern}");
        }

        foreach ($includeAndPatterns as $pattern) {
            $query->where('name', 'like', "%{$pattern}%");
        }
        if (! empty($includeOrPatterns)) {
            $query->where(function ($q) use ($includeOrPatterns) {
                foreach ($includeOrPatterns as $pattern) {
                    $q->orWhere('name', 'like', "%{$pattern}%");
                }
            });
        }

        foreach ($excludePatterns as $pattern) {
            $query->where('name', 'not like', "%{$pattern}%");
        }

        $query->orderBy('name', 'asc');
        return $query
            ->pluck('name', 'id')
            ->mapWithKeys(function ($label, $id) {
                $translationKey = str_replace('::', '_', $label);
                return [$id => __('permissions.' . $translationKey)];
            })
            ->sortBy(fn($label) => $label)
            ->toArray();
    }
}

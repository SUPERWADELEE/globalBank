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
                        // 白名單
                        Fieldset::make(__('system_management.title'))->schema([
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(
                                            fn($p) =>
                                            str_contains($p->name, 'admin::ip::white::list') &&
                                                !str_contains($p->name, 'view_admin::ip::white::list')
                                        )
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(function ($label, $id) {
                                            $translationKey = str_replace('::', '_', $label); // 將 :: 換成 _
                                            return [$id => __('permissions.' . $translationKey)];
                                        })
                                        ->toArray()
                                )
                                ->bulkToggleable()
                                ->label(__('permissions.groups.whitelist')),
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(fn($p) => str_contains($p->name, 'admin::user') && !str_contains($p->name, 'view_admin::user') && !str_contains($p->name, 'delete_admin::user'))
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(function ($label, $id) {
                                            $translationKey = str_replace('::', '_', $label); // 將 :: 換成 _
                                            return [$id => __('permissions.' . $translationKey)];
                                        })
                                        ->toArray()
                                )
                                ->bulkToggleable()
                                ->label(__('permissions.groups.admin_user')),
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(fn($p) => str_contains($p->name, 'role') && !str_contains($p->name, 'view_role') && !str_contains($p->name, 'delete_role'))
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(fn($label, $id) => [$id => __('permissions.' . $label)])
                                        ->toArray()
                                )
                                ->bulkToggleable()
                                ->label(__('role.title')),

                        ]),
                        // 交易紀錄
                        Fieldset::make(__('transaction.title'))->schema([
                            // 存款
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(fn($p) => str_contains($p->name, 'view_any_deposit::log'))
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(function ($label, $id) {
                                            $translationKey = str_replace('::', '_', $label); // 將 :: 換成 _
                                            return [$id => __('permissions.' . $translationKey)];
                                        })
                                        ->toArray()
                                )
                                ->label(__('deposit.title'))
                                ->bulkToggleable(),
                            // 提款
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(fn($p) => str_ends_with($p->name, 'view_any_withdraw'))
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(fn($label, $id) => [$id => __('permissions.' . $label)])
                                        ->toArray()
                                )
                                ->label(__('withdraw.title'))
                                ->bulkToggleable(),

                        ]),

                        // 訂單管理
                        Fieldset::make(__('common.order_management'))->schema([
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(fn($p) => str_contains($p->name, 'view_any_deposit::order'))
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(function ($label, $id) {
                                            $translationKey = str_replace('::', '_', $label); // 將 :: 換成 _
                                            return [$id => __('permissions.' . $translationKey)];
                                        })
                                        ->toArray()
                                )
                                ->label(__('deposit.order.title'))
                                ->bulkToggleable(),
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(fn($p) => str_contains($p->name, 'view_any_withdraw::order'))
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(function ($label, $id) {
                                            $translationKey = str_replace('::', '_', $label); // 將 :: 換成 _
                                            return [$id => __('permissions.' . $translationKey)];
                                        })
                                        ->toArray()
                                )
                                ->label(__('withdraw.order.title'))
                                ->bulkToggleable(),
                        ]),
                        Fieldset::make(__('permissions.groups.rate'))->schema([
                            CheckboxList::make('permissions')
                                ->label(__('permissions.groups.rate'))
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(function ($p) {
                                            return (str_contains($p->name, 'view_any_rate') || str_contains($p->name, 'edit_')) && str_ends_with($p->name, 'rate');
                                        })
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(function ($label, $id) {
                                            $translationKey = str_replace('::', '_', $label); // 將 :: 換成 _
                                            return [$id => __('permissions.' . $translationKey)];
                                        })
                                )
                                ->bulkToggleable(),
                        ]),
                        Fieldset::make(__('user.user_management'))->schema([
                            CheckboxList::make('permissions')
                                ->label(__('user.user_management'))
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(function ($p) {
                                            return str_ends_with($p->name, '_user') && !str_contains($p->name, 'view_any_user') && !str_contains($p->name, 'delete_user');
                                        })
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(fn($label, $id) => [$id => __('permissions.' . $label)])
                                        ->toArray()
                                )->bulkToggleable(),
                            CheckboxList::make('permissions')
                                ->label(__('user.account_operation'))
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(function ($p) {
                                            return str_ends_with($p->name, 'user_wallet');
                                        })
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(fn($label, $id) => [$id => __('permissions.' . $label)])
                                        ->toArray()
                                )->bulkToggleable(),
                            CheckboxList::make('permissions')
                                ->label(__('user.operation_log'))
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(function ($p) {
                                            return str_ends_with($p->name, 'user_wallet_logs');
                                        })
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(fn($label, $id) => [$id => __('permissions.' . $label)])
                                        ->toArray()
                                )->bulkToggleable(),
                        ]),
                        Fieldset::make(__('platform_wallet.title.platform_wallet'))->schema([
                            CheckboxList::make('permissions')
                                ->label(__('platform_wallet.title.platform_wallet'))
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(function ($p) {
                                            return str_contains($p->name, 'view_any_platform::wallet');
                                        })
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(function ($label, $id) {
                                            $translationKey = str_replace('::', '_', $label); // 將 :: 換成 _
                                            return [$id => __('permissions.' . $translationKey)];
                                        })
                                        ->toArray()
                                )->bulkToggleable(),
                        ]),
                        Fieldset::make(__('admin_user.account_settings'))->schema([
                            CheckboxList::make('permissions')
                                ->label(__('admin_user.account_settings'))
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(function ($p) {
                                            return str_contains($p->name, 'account_settings');
                                        })
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(fn($label, $id) => [$id => __('permissions.' . $label)])
                                        ->toArray()
                                )->bulkToggleable(),
                        ])


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
                Tables\Filters\SelectFilter::make('name')
                    ->label(__('role.role_name'))
                    ->options(Role::pluck('name', 'name')->toArray())
                    ->searchable(),
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
}

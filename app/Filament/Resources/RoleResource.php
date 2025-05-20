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
                                        ->filter(fn($p) => str_contains($p->name, 'admin::ip::white::list'))
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
                                        ->filter(fn($p) => str_contains($p->name, 'admin::user'))
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
                                        ->filter(fn($p) => str_contains($p->name, 'role'))
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
                                        ->filter(fn($p) => str_contains($p->name, 'deposit::log'))
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
                                        ->filter(fn($p) => str_ends_with($p->name, 'withdraw'))
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
                                        ->filter(fn($p) => str_contains($p->name, 'deposit::order'))
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
                                        ->filter(fn($p) => str_contains($p->name, 'withdraw::order'))
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
                            // CheckboxList::make('permissions')
                            //     ->relationship('permissions', 'name')
                            //     ->columns(4)
                            //     ->options(
                            //         \Spatie\Permission\Models\Permission::all()
                            //             ->filter(function ($p) {
                            //                 return str_contains($p->name, 'usdt_rate') ||
                            //                     str_contains($p->name, 'jpy_rate') ||
                            //                     str_contains($p->name, 'sgd_rate') ||
                            //                     str_contains($p->name, 'krw_rate');
                            //             })
                            //             ->pluck('name', 'id')
                            //             ->mapWithKeys(fn($label, $id) => [$id => __('permissions.' . $label)])
                            //             ->toArray()
                            //     )->bulkToggleable(), 
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(function ($p) {
                                            return str_contains($p->name, 'rate');
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
                                            return str_ends_with($p->name, '_user');
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
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(function ($p) {
                                            return str_contains($p->name, 'platform::wallet');
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
                    ->label(__('admin_user.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('users.name')
                    ->label(__('admin_user.admin_user'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('name')
                    ->label(__('admin_user.name'))
                    ->options(Role::pluck('name', 'name')->toArray()),
                Tables\Filters\SelectFilter::make('created_at')
                    ->label(__('common.created_at'))
                    ->options(Role::pluck('created_at', 'created_at')->toArray()),
                Tables\Filters\SelectFilter::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->options(Role::pluck('updated_at', 'updated_at')->toArray()),
                //
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
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

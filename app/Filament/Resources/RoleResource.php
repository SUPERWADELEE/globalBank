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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                        Fieldset::make(__('permissions.groups.system'))->schema([
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(fn($p) => str_contains($p->name, 'admin_user'))
                                        ->pluck('name', 'id') // ✔️ 正確：key 是 id
                                        ->mapWithKeys(fn($label, $id) => [$id => __('permissions.' . $label)])
                                        ->toArray()
                                )
                                ->bulkToggleable(),
                        ]),

                        Fieldset::make(__('permissions.groups.system'))->schema([
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(fn($p) => str_starts_with($p->name, 'system.'))
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(fn($label, $id) => [$id => __('permissions.' . $label)])
                                        ->toArray()
                                )
                                ->bulkToggleable(),
                        ]),

                        Fieldset::make(__('permissions.groups.whitelist'))->schema([
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(fn($p) => str_contains($p->name, 'admin_ip_white_list'))
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(fn($label, $id) => [$id => __('permissions.' . $label)])
                                        ->toArray()
                                )
                                ->bulkToggleable(),
                        ]),

                        Fieldset::make(__('permissions.groups.member'))->schema([
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(fn($p) => str_contains($p->name, 'user'))
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(fn($label, $id) => [$id => __('permissions.' . $label)])
                                        ->toArray()
                                )
                                ->bulkToggleable(),
                        ]),

                        Fieldset::make(__('permissions.groups.role'))->schema([
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
                                ->bulkToggleable(),
                        ]),

                        Fieldset::make(__('permissions.groups.permission'))->schema([
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(fn($p) => str_contains($p->name, 'permission'))
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(fn($label, $id) => [$id => __('permissions.' . $label)])
                                        ->toArray()
                                )
                                ->bulkToggleable(),
                        ]),
                        Fieldset::make(__('permissions.groups.permission'))->schema([
                            CheckboxList::make('permissions')
                                ->relationship('permissions', 'name')
                                ->columns(4)
                                ->options(
                                    \Spatie\Permission\Models\Permission::all()
                                        ->filter(function ($p) {
                                            return str_contains($p->name, 'usdt_rate') ||
                                                str_contains($p->name, 'jpy_rate') ||
                                                str_contains($p->name, 'sgd_rate') ||
                                                str_contains($p->name, 'krw_rate');
                                        })
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(fn($label, $id) => [$id => __('permissions.' . $label)])
                                        ->toArray()
                                )
                                ->bulkToggleable(),
                        ]),

                        // 再加上財務操作、交易紀錄、訂單管理等群組

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


    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
        ];
    }
}

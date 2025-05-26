<?php

namespace App\Filament\Resources;

use Filament\Tables\Enums\FiltersLayout;
use App\Filament\Resources\AdminUserResource\Pages;
use App\Models\AdminUser;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\DB;
use App\Enums\LocaleEnum;
use App\Models\AdminUserTeam;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use App\Filament\Filters\CommonFilters;

class AdminUserResource extends Resource
{
    protected static ?string $model = AdminUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    public static function getNavigationLabel(): string
    {
        return __('admin_user.navigation.account_management');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('system_management.title');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('admin_user.username'))
                    ->required()
                    ->maxLength(255)
                    ->rules(['regex:/^[\pL\pN\s]+$/u']), // 只允許字母（含中英文）、數字與空白
                Select::make('roles')
                    ->label(__('admin_user.roles'))
                    ->options(Role::all()->pluck('name', 'id'))
                    ->required()
                    ->afterStateHydrated(function ($component, $state, ?AdminUser $record) {
                        // 如果是編輯現有記錄
                        if ($record) {
                            // 從數據庫中獲取該AdminUser的角色ID
                            $roleIds = DB::table('model_has_roles')
                                ->where('model_id', $record->id)
                                ->where('model_type', 'App\\Models\\AdminUser')
                                ->pluck('role_id')
                                ->toArray();

                            // 設置選中的角色
                            $component->state($roleIds);
                        }
                    }),
                TextInput::make('job_title')
                    ->label(__('admin_user.job_title'))
                    ->required()
                    ->maxLength(255),
                Select::make('team_id')
                    ->label(__('admin_user.team.name'))
                    ->options(AdminUserTeam::all()->pluck('name', 'id'))
                    ->required(),

                TextInput::make('password')
                    ->label(__('admin_user.password'))
                    ->password()
                    ->required(fn($livewire) => $livewire instanceof Pages\CreateAdminUser)
                    ->dehydrated(fn($state) => filled($state))
                    ->maxLength(255)
                    ->revealable()
                    ->suffixAction(
                        Action::make('generatePassword')
                            ->tooltip(__('user.random_password'))
                            ->icon('heroicon-o-sparkles')
                            ->color('secondary')
                            ->action(
                                fn(Set $set) =>
                                $set('password', Str::random(12))      // 寫回欄位
                            )
                    )
                    ->rule('confirmed'),

                TextInput::make('password_confirmation')
                    ->label(__('admin_user.confirm_password'))
                    ->password()
                    ->required(fn($livewire) => $livewire instanceof Pages\CreateAdminUser)
                    ->dehydrated(fn($state) => filled($state))
                    ->maxLength(255)
                    ->revealable(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin_user.username')),
                TextColumn::make('job_title')
                    ->label(__('admin_user.job_title')),
                TextColumn::make('roles.name')
                    ->label(__('role.role_name')),
                TextColumn::make('team.name')
                    ->label(__('admin_user.team.name')),
            ])
            ->filters([
                CommonFilters::textLike('name', __('admin_user.name'), __('common.placeholder')),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('admin_user.edit')),
                Tables\Actions\Action::make('操作日誌')
                    ->url(fn($record) => route('filament.admin.resources.admin-logs.index', ['causer_id' => $record->id]))
                    ->label(__('admin_user.operation_log'))
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
            'index' => Pages\ListAdminUsers::route('/'),
            'create' => Pages\CreateAdminUser::route('/create'),
            'edit' => Pages\EditAdminUser::route('/{record}/edit'),
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

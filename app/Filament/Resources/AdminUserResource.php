<?php

namespace App\Filament\Resources;

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


class AdminUserResource extends Resource
{
    protected static ?string $model = AdminUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // protected static ?string $navigationLabel = __('admin_user.navigation.system_settings');
    public static function getNavigationLabel(): string
    {
        return __('admin_user.navigation.system_settings');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('admin_user.name'))
                    ->required()
                    ->maxLength(255)
                    ->rules(['regex:/^[\pL\pN\s]+$/u']), // 只允許字母（含中英文）、數字與空白

                TextInput::make('email')
                    ->label(__('admin_user.email'))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
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
                Select::make('locale')
                    ->label(__('admin_user.locale'))
                    ->options(collect(LocaleEnum::cases())->mapWithKeys(fn($case) => [
                        $case->value => $case->label()
                    ])->toArray())
                    ->default(LocaleEnum::TraditionalChinese->value)
                    ->required(),

                TextInput::make('password')
                    ->label(__('admin_user.password'))
                    ->password()
                    ->required(fn($livewire) => $livewire instanceof Pages\CreateAdminUser)
                    ->dehydrated(fn($state) => filled($state))
                    ->maxLength(255),
                    
                TextInput::make('password_confirmation')
                    ->label(__('admin_user.confirm_password'))
                    ->password()
                    ->required(fn($livewire) => $livewire instanceof Pages\CreateAdminUser)
                    ->dehydrated(fn($state) => filled($state))
                    ->maxLength(255)
                    ->rule('confirmed'),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin_user.name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('admin_user.email'))
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label(__('admin_user.roles'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime('Y-m-d H:i:s')
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->dateTime('Y-m-d H:i:s')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('name')
                    ->label(__('admin_user.name'))
                    ->options(function () {
                        // 從資料庫獲取所有不重複的用戶名稱
                        return \App\Models\AdminUser::pluck('name', 'name')->toArray();
                    }),

                Tables\Filters\SelectFilter::make('email')
                    ->label(__('admin_user.email'))
                    ->options(function () {
                        return \App\Models\AdminUser::pluck('email', 'email')->toArray();
                    }),

                Tables\Filters\SelectFilter::make('roles.name')
                    ->label(__('admin_user.roles'))
                    ->options(function () {
                        return \Spatie\Permission\Models\Role::pluck('name', 'name')->toArray();
                    })
                    ->relationship('roles', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('admin_user.edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('admin_user.delete')),
                Tables\Actions\Action::make('操作日誌')
                    ->url(route('filament.admin.resources.admin-logs.index'))
                    ->label(__('admin_user.operation_log'))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('admin_user.delete')),
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

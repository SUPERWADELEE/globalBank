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

class AdminUserResource extends Resource
{
    protected static ?string $model = AdminUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = '系統設置';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->rules(['regex:/^[\pL\pN\s]+$/u']), // 只允許字母（含中英文）、數字與空白

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('roles')
                    ->multiple()
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
                TextInput::make('password')
                    ->password()
                    ->required(fn($livewire) => $livewire instanceof Pages\CreateAdminUser)
                    ->dehydrated(fn($state) => filled($state))
                    ->maxLength(255),
                TextInput::make('password_confirmation')
                    ->password()
                    ->required(fn($livewire) => $livewire instanceof Pages\CreateAdminUser)
                    ->dehydrated(fn($state) => filled($state))
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('roles.name')
                    ->label('角色'),
                TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i:s'),
                TextColumn::make('updated_at')
                    ->dateTime('Y-m-d H:i:s'),
            ])
            ->filters([
                //
            ])
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

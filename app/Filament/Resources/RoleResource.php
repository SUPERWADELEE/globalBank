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

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $modelLabel = 'Role';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('群組名稱')
                    ->required(),

                Section::make('權限分配')->schema([
                    Grid::make(3)->schema([
                        Fieldset::make('會員管理')->schema([
                            Forms\Components\Select::make('permissions')
                                ->relationship('permissions', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->getOptionLabelFromRecordUsing(fn($record) => __('permissions.' . $record->name)),
                        ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('users.name')
                    ->label(__('admin_user.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-m-Y H:i')
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-m-Y H:i')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('name')
                    ->label(__('admin_user.name'))
                    ->options(Role::all()->pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('users.name')
                    ->label(__('admin_user.name'))
                    ->options(Role::all()->pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('created_at')
                    ->label(__('common.created_at'))
                    ->options(Role::all()->pluck('created_at', 'id')),
                Tables\Filters\SelectFilter::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->options(Role::all()->pluck('updated_at', 'id')),
                //
            ])
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
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}

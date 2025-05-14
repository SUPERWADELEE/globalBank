<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                Checkbox::make('status')
                    ->default(true),
                Select::make('roles')
                    ->multiple()
                    ->required(),
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('角色'),
                Tables\Columns\TextColumn::make('permissions.name')
                    ->label('權限'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-m-Y H:i'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-m-Y H:i'),
                //
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles.name')
                    ->label('角色'),
                Tables\Filters\SelectFilter::make('permissions.name')
                    ->label('權限'),
                Tables\Filters\SelectFilter::make('created_at')
                    ->label('建立時間'),
                Tables\Filters\SelectFilter::make('updated_at')
                    ->label('更新時間'),
                Tables\Filters\SelectFilter::make('name')
                    ->label('姓名'),
                Tables\Filters\SelectFilter::make('email')
                    ->label('電子郵件'),
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

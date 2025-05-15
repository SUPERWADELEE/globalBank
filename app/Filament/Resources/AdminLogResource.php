<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminLogResource\Pages;
use App\Filament\Resources\AdminLogResource\RelationManagers;
use App\Models\AdminLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdminLogResource extends Resource
{
    protected static ?string $model = AdminLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('admin_user.name'),
                TextColumn::make('action'),
                TextColumn::make('content'),
                TextColumn::make('ip'),
                TextColumn::make('user_agent'),
                TextColumn::make('created_at'),
                //
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
            'index' => Pages\ListAdminLogs::route('/'),
            'create' => Pages\CreateAdminLog::route('/create'),
            'edit' => Pages\EditAdminLog::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}

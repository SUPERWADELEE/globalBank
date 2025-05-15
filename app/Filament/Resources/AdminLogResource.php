<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminLogResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Spatie\Activitylog\Models\Activity;

class AdminLogResource extends Resource
{
    protected static ?string $model = Activity::class;

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
                TextColumn::make('description')->label('動作')->searchable(),
                TextColumn::make('causer.name')->label('操作者')->searchable(),
                TextColumn::make('subject_id')->label('對象 ID')->searchable(),
                TextColumn::make('created_at')->label('時間')->since()->searchable(),
                // 🔹 新值
                TextColumn::make('properties.attributes')
                    ->label('New')->searchable(),

                // 🔸 舊值
                TextColumn::make('properties.old')
                    ->label('Old')->searchable(),
            ])

            ->defaultSort('created_at', 'desc')
            // ->columns([
            //     TextColumn::make('description'),
            //     TextColumn::make('created_at'),
            // ])
            ->filters([
                Tables\Filters\SelectFilter::make('causer.name')
                    ->label('操作者')
                    ->options(Activity::all()->pluck('causer.name', 'causer.id')),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('對象 ID')
                    ->options(Activity::all()->pluck('subject_id', 'subject_id')),
                Tables\Filters\SelectFilter::make('description')
                    ->label('動作')
                    ->options(Activity::all()->pluck('description', 'description')),
                //
            ])
            ->actions([])
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

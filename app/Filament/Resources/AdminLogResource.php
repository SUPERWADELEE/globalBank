<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminLogResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Spatie\Activitylog\Models\Activity;
use Filament\Tables\Enums\FiltersLayout;

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
                TextColumn::make('causer.name')->label('操作者')->searchable(),
                TextColumn::make('description')
                    ->label('操作紀錄')
                    ->formatStateUsing(function ($state, $record) {
                        $causerName = optional($record->causer)->name ?? '未知操作者';
                        $subjectName = optional($record->subject)->name ?? '未知對象';
                        $event = $record->event;

                        return __('activity.log_description', [
                            'causer' => $causerName,
                            'subject' => $subjectName,
                            'event' => $event,
                        ]);
                    }),
                TextColumn::make('subject')
                    ->label('單元')
                    ->formatStateUsing(function ($state, $record) {
                        $subjectType = $record->subject_type;
                        $event = $record->event;


                        // 從語系取出對應文字
                        $module = __('activity.subject_modules.' . $subjectType);
                        $action = __('activity.event_names.' . $event);
                        // 如果沒有找到語系就顯示原始型別
                        $module = $module !== "activity.subject_modules.$subjectType" ? $module : class_basename($subjectType);
                        $action = $action !== "activity.event_names.deleted" ? $action : $event;

                        return "{$module} - {$action}";
                    }),

                TextColumn::make('created_at')->label('時間')->since()->searchable(),
                // 🔹 新值
                TextColumn::make('properties.attributes')
                    ->label('New')->searchable(),

                // 🔸 舊值
                TextColumn::make('properties.old')
                    ->label('Old')->searchable(),
            ])

            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('causer_id')
                    ->label('操作者')
                    ->options(
                        \App\Models\AdminUser::pluck('name', 'id')
                    ),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('對象 ID')
                    ->options(Activity::all()->pluck('subject_id', 'subject_id')),
                Tables\Filters\SelectFilter::make('description')
                    ->label('動作')
                    ->options(Activity::all()->pluck('description', 'description')),
                //
            ], layout: FiltersLayout::AboveContent)
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

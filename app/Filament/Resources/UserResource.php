<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use App\Models\UserLevel;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Enums\UserStatus;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Select::make('user_level_id')
                    ->options(UserLevel::all()->pluck('name', 'id'))
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('otp_secret')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('status')
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password_confirmation')
                    ->required()
                    ->maxLength(255),
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('name')->label('帳號')->sortable()->searchable(),
            TextColumn::make('email')->label('E-mail')->sortable()->searchable(),
            TextColumn::make('phone')->label('電話')->sortable()->searchable(),
            TextColumn::make('userLevel.name')->label('會員等級')->sortable()->searchable(),
            TextColumn::make('status')
                ->label('狀態')
                ->badge(),
            TextColumn::make('created_at')
                ->label('註冊日期')
                ->dateTime('Y/m/d H:i'),
        ])
        ->filters([
            // 🔹 註冊地點
            SelectFilter::make('register_location')
                ->label('註冊地點')
                ->options([
                    '東京01' => '東京01',
                    '韓國02' => '韓國02',
                ]),
        
            // 🔹 會員等級（關聯欄位）
            SelectFilter::make('user_level_id')
                ->label('會員等級')
                ->relationship('userLevel', 'name'),
        
            // 🔹 搜尋帳號或 Email
            Filter::make('search_keyword')
                ->form([
                    TextInput::make('keyword')
                        ->label('帳號或 Email')
                        ->placeholder('輸入帳號或 Email'),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['keyword'], fn ($q, $keyword) =>
                            $q->where(fn ($sub) =>
                                $sub->where('name', 'like', "%{$keyword}%")
                                    ->orWhere('email', 'like', "%{$keyword}%")
                            )
                        );
                }),
        
            // 🔹 註冊日期區間
            Filter::make('created_at')
                ->form([
                    DatePicker::make('from')->label('開始時間'),
                    DatePicker::make('until')->label('結束時間'),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                }),
        ])
        ->actions([
            Action::make('toggleFreeze')
                ->label(fn ($record) => $record->status === UserStatus::Frozen ? '解凍' : '凍結')
                ->color(fn ($record) => $record->status === UserStatus::Frozen ? 'success' : 'warning')
                ->action(function ($record) {
                    $record->status = $record->status === UserStatus::Frozen
                        ? UserStatus::Active
                        : UserStatus::Frozen;
                    $record->save();
                    Notification::make()
                        ->title('狀態已更新')
                        ->success()
                        ->send();
                }),

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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

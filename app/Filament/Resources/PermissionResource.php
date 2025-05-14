<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Permission;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Checkbox;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('權限名稱')
                    ->required(),

                Section::make('權限群組分類')->schema([
                    Grid::make(3)->schema([

                        Fieldset::make('會員管理')->schema([
                            Checkbox::make('create_member')->label('創立會員'),
                            Checkbox::make('edit_member')->label('修改會員'),
                            Checkbox::make('finance_in')->label('財務操作-入金'),
                            Checkbox::make('finance_out')->label('財務操作-出金'),
                        ]),

                        Fieldset::make('財務操作-入金')->schema([
                            Checkbox::make('deposit_usdt')->label('USDT'),
                            Checkbox::make('deposit_krw')->label('KRW'),
                            Checkbox::make('deposit_sgd')->label('SGD'),
                            Checkbox::make('deposit_jpy')->label('JPY'),
                        ]),

                        Fieldset::make('財務操作-出金')->schema([
                            Checkbox::make('withdraw_usdt')->label('USDT'),
                            Checkbox::make('withdraw_krw')->label('KRW'),
                            Checkbox::make('withdraw_sgd')->label('SGD'),
                            Checkbox::make('withdraw_jpy')->label('JPY'),
                        ]),

                        Fieldset::make('匯率設定')->schema([
                            Checkbox::make('rate_usdt')->label('USDT'),
                            Checkbox::make('rate_krw')->label('KRW'),
                            Checkbox::make('rate_sgd')->label('SGD'),
                            Checkbox::make('rate_jpy')->label('JPY'),
                        ]),

                        Fieldset::make('訂單管理')->schema([
                            Checkbox::make('order_in')->label('入金訂單'),
                            Checkbox::make('order_out')->label('出金訂單'),
                        ]),

                        Fieldset::make('交易紀錄')->schema([
                            Checkbox::make('record_exchange')->label('換匯紀錄'),
                            Checkbox::make('record_in')->label('入金紀錄'),
                            Checkbox::make('record_out')->label('出金紀錄'),
                        ]),

                        Fieldset::make('系統設定')->schema([
                            Checkbox::make('system_account')->label('系統帳號管理'),
                            Checkbox::make('system_role')->label('權限設置'),
                            Checkbox::make('system_whitelist')->label('白名單設定'),
                            Checkbox::make('system_balance')->label('查看平台餘額'),
                        ]),
                    ])
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-m-Y H:i'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-m-Y H:i'),

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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}

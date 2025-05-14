<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\CheckboxList;
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
                                ->searchable(),

                        ]),

                        // Fieldset::make('財務操作 - 入金')->schema([
                        //     CheckboxList::make('permissions')
                        //         ->relationship('permissions', 'name')
                        //         ->label('')
                        //         ->options([
                        //             'finance.deposit.usdt' => 'USDT',
                        //             'finance.deposit.krw' => 'KRW',
                        //             'finance.deposit.sgd' => 'SGD',
                        //             'finance.deposit.jpy' => 'JPY',
                        //         ])
                        // ]),

                        // Fieldset::make('財務操作 - 出金')->schema([
                        //     CheckboxList::make('permissions')
                        //         ->relationship('permissions', 'name')
                        //         ->label('')
                        //         ->options([
                        //             'finance.withdraw.usdt' => 'USDT',
                        //             'finance.withdraw.krw' => 'KRW',
                        //             'finance.withdraw.sgd' => 'SGD',
                        //             'finance.withdraw.jpy' => 'JPY',
                        //         ])
                        // ]),

                        // Fieldset::make('系統設定')->schema([
                        //     CheckboxList::make('permissions')
                        //         ->relationship('permissions', 'name')
                        //         ->label('')
                        //         ->options([
                        //             'system.accounts' => '系統帳號管理',
                        //             'system.roles' => '權限設定',
                        //             'system.whitelist' => '白名單設定',
                        //             'system.balance' => '查看平台餘額',
                        //         ])
                        // ]),

                        // 其他群組區塊依照需求加上去
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('users.name')
                    ->label('會員'),
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
}

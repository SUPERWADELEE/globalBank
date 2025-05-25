<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminIpWhiteListResource\Pages;

use App\Models\AdminIpWhitelist;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class AdminIpWhiteListResource extends Resource
{
    protected static ?string $model = AdminIpWhitelist::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    public static function getNavigationGroup(): ?string
    {
        return __('system_management.title');
    }
    public static function getNavigationLabel(): string
    {
        return __('admin_user.navigation.ip_white_list');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('ip_address')
                    ->required()
                    ->maxLength(255)
                    ->label(__('admin_ip_white_list.ip_address'))
                    ->rule([
                        'ip',
                        Rule::unique('admin_ip_whitelists', 'ip_address')->whereNull('deleted_at'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ip_address')
                    ->label(__('admin_ip_white_list.ip_address')),
                TextColumn::make('adminUser.name')
                    ->label(__('admin_user.username'))
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ip_address')
                    ->label(__('admin_ip_white_list.ip_address'))
                    ->options(AdminIpWhitelist::pluck('ip_address', 'ip_address')->toArray())->searchable(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->label(__('admin_user.delete')),
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
            'index' => Pages\ListAdminIpWhiteLists::route('/'),
            'create' => Pages\CreateAdminIpWhiteList::route('/create'),

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

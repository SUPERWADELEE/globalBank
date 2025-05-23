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
                    ->rule('ip'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        // ->query(fn (Builder $query) => $query->with('adminUser'))
        ->columns([
            TextColumn::make('ip_address')
                ->label(__('admin_ip_white_list.ip_address')),
            TextColumn::make('adminUser.username')          
                ->label(__('admin_user.username')),
        ])
            ->filters([
                Tables\Filters\SelectFilter::make('ip_address')
                    ->label(__('admin_ip_white_list.ip_address'))
                    ->options(AdminIpWhiteList::pluck('ip_address', 'ip_address')->toArray()),
                Tables\Filters\SelectFilter::make('created_at')
                    ->label(__('common.created_at'))
                    ->options(AdminIpWhiteList::pluck('created_at', 'created_at')->toArray()),
                Tables\Filters\SelectFilter::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->options(AdminIpWhiteList::pluck('updated_at', 'updated_at')->toArray()),
                //
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
            'edit' => Pages\EditAdminIpWhiteList::route('/{record}/edit'),
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

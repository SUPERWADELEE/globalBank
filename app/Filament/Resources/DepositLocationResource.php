<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepositLocationResource\Pages;
use App\Models\DepositLocation;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Enums\DepositCode;
use App\Enums\DepositChannel;
use App\Enums\DepositLocationStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rule;

class DepositLocationResource extends Resource
{
    protected static ?string $model = DepositLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationLabel(): string
    {
        return __('admin_user.deposit_location.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('usdt_setting.navigation.usdt_settings');
    }

    public static function form(Form $form): Form
    {
        $isEdit = request()->routeIs('filament.admin.resources.deposit-locations.edit');

        return $form
            ->schema([
                Select::make('currency_code_id')
                    ->label(__('admin_user.deposit_location.currency_code'))
                    ->options(DepositCode::class)
                    ->rules(['required'])
                    ->markAsRequired()
                    ->visible(!$isEdit),

                TextInput::make('location')
                    ->label(__('admin_user.deposit_location.location'))
                    ->markAsRequired()
                    ->visible(!$isEdit)
                    ->maxLength(34)
                    ->rule([
                        'required',
                        'min:34',
                        'regex:/^T[a-zA-Z0-9]{33}$/',
                        Rule::unique('deposit_locations', 'location')->whereNull('deleted_at')->ignore($isEdit ? $form->model->id : null),
                    ])
                    ->validationMessages([
                        'regex' => __('deposit_location.location_is_incorrect'),
                        'min' => __('deposit_location.location_is_too_short'),
                    ]),
                

                Select::make('channel')
                    ->label(__('admin_user.deposit_location.channel'))
                    ->options(DepositChannel::class)
                    ->rules(['required'])
                    ->markAsRequired()
                    ->visible(!$isEdit),

                Select::make('status')
                    ->label(__('admin_user.deposit_location.status'))
                    ->options(DepositLocationStatus::class)
                    ->rules(['required'])
                    ->markAsRequired(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('currencyCode.code')
                    ->label(__('admin_user.deposit_location.currency_code')),
                TextColumn::make('location')
                    ->label(__('admin_user.deposit_location.location')),
                TextColumn::make('channel')
                    ->label(__('admin_user.deposit_location.channel')),
                ToggleColumn::make('status')
                    ->label(__('admin_user.deposit_location.status'))
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->onColor('success')
                    ->offColor('gray')
                    ->getStateUsing(fn($record): bool => $record->status === DepositLocationStatus::Enable)
                    ->afterStateUpdated(function ($record, $state) {
                        $record->status = $state ? DepositLocationStatus::Enable : DepositLocationStatus::Disable;
                        $record->save();
                        Notification::make()
                            ->title(__('deposit_location.status_updated', ['location' => $record->location]))
                            ->success()
                            ->send();

                        if ($state) {
                            \App\Models\DepositLocation::where('id', '!=', $record->id)
                                ->where('status', DepositLocationStatus::Enable)
                                ->update(['status' => DepositLocationStatus::Disable]);
                        }
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                DeleteAction::make(),
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
            'index' => Pages\ListDepositLocations::route('/'),
            'create' => Pages\CreateDepositLocation::route('/create'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['currencyCode']);
    }
}

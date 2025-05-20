<?php

namespace App\Filament\Resources\DepositLocationResource\Pages;

use App\Filament\Resources\DepositLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepositLocations extends ListRecords
{
    protected static string $resource = DepositLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('admin_user.deposit_location.create')),

        ];
    }
    public function getTitle(): string
    {
        return __('admin_user.deposit_location.title');
    }
}

<?php

namespace App\Filament\Resources\RateResource\Pages;

use App\Filament\Resources\RateResource;

use Filament\Resources\Pages\EditRecord;

class EditRate extends EditRecord
{
    protected static string $resource = RateResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
    public function getTitle(): string
    {
        return __('rate.edit_rate');
    }
}

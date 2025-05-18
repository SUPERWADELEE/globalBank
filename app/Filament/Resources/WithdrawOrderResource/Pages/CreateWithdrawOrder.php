<?php

namespace App\Filament\Resources\WithdrawOrderResource\Pages;

use App\Filament\Resources\WithdrawOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWithdrawOrder extends CreateRecord
{
    protected static string $resource = WithdrawOrderResource::class;
}

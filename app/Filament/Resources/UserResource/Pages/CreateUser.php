<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
       $now = Carbon::now();
       DB::table('wallets')->insert([
        'user_id' => $this->record->id,
        'currency_code_id' => 1,
        'balance' => 0,
        'created_at' => $now,
        'updated_at' => $now,
       ]);
       DB::table('wallets')->insert([
        'user_id' => $this->record->id,
        'currency_code_id' => 3,
        'balance' => 0,
        'created_at' => now(),
        'updated_at' => now(),
       ]);
       DB::table('wallets')->insert([
        'user_id' => $this->record->id,
        'currency_code_id' => 2,
        'balance' => 0,
        'created_at' => $now,
        'updated_at' => $now,
       ]);
       DB::table('wallets')->insert([
        'user_id' => $this->record->id,
        'currency_code_id' => 4,
        'balance' => 0,
        'created_at' => $now,
        'updated_at' => $now,
       ]);
    }
}

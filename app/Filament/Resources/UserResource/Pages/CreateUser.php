<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use RobThree\Auth\TwoFactorAuth;
use App\Models\Wallet;
use App\Models\CurrencyCode;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['otp_secret'] = (new TwoFactorAuth(new \RobThree\Auth\Providers\Qr\EndroidQrCodeProvider()))->createSecret();
        return $data;
    }

    protected function afterCreate(): void
    {
        foreach (CurrencyCode::all() as $currency) {
            Wallet::create([
                'user_id' => $this->record->id,
                'currency_code_id' => $currency->id,
                'balance' => 0,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurrencyCode;
use App\Models\PlatformWallet;

class PlatformWalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencyCodes = CurrencyCode::all();
        foreach ($currencyCodes as $currencyCode) {
            PlatformWallet::firstOrCreate(
                [
                    'currency_code_id' => $currencyCode->id,
                ],
                [
                    'amount' => 0,
                ],
            );
        }
    }
}

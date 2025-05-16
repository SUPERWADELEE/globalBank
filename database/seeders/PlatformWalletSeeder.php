<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CurrencyCode;
use Carbon\Carbon;
class PlatformWalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $currencyCodes = CurrencyCode::all();
        foreach ($currencyCodes as $currencyCode) {
            DB::table('platform_wallets')->insert([
                'amount' => 0,
                'currency_code_id' => $currencyCode->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}

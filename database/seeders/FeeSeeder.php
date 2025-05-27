<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fee;
use App\Models\CurrencyCode;

class FeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Fee::firstOrCreate([
            'currency_code_id' => CurrencyCode::USDT_ID,
            'amount' => 0,
        ]);
    }
}

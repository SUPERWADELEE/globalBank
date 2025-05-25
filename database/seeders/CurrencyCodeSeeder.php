<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\CurrencyCode;

class CurrencyCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['code' => 'JPY', 'name' => '日圓'],
            ['code' => 'SGD', 'name' => '新加坡幣'],
            ['code' => 'KRW', 'name' => '韓元'],
            ['code' => 'USDT', 'name' => '泰達幣'],
        ];

        foreach ($currencies as $currency) {
            CurrencyCode::firstOrCreate(
                [
                    'code' => $currency['code'],
                ],
                [
                    'name' => $currency['name'],
                ],
            );
        }
    }
}

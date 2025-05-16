<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CurrencyCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $currencies = [
            ['code' => 'JPY', 'name' => '日圓'],
            ['code' => 'SGD', 'name' => '新加坡幣'],
            ['code' => 'KRW', 'name' => '韓元'],
            ['code' => 'USDT', 'name' => '泰達幣'],
        ];

        foreach ($currencies as $currency) {
            DB::table('currency_codes')->insert([
                'code' => $currency['code'],
                'name' => $currency['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}

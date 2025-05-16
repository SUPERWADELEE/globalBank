<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RateSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 先查出所有幣別名稱對應的 id
        $currencies = DB::table('currency_codes')->pluck('id', 'code');
        $rates = [
            ['from' => 'USDT', 'to' => 'SGD', 'sell_rate' => 1.15, 'buy_rate' => 1.10],
            ['from' => 'USDT', 'to' => 'JPY', 'sell_rate' => 145, 'buy_rate' => 142],
            ['from' => 'USDT', 'to' => 'KRW', 'sell_rate' => 1418, 'buy_rate' => 1410],
            ['from' => 'SGD', 'to' => 'KRW', 'sell_rate' => 1100, 'buy_rate' => 1086],
            ['from' => 'SGD', 'to' => 'JPY', 'sell_rate' => 109, 'buy_rate' => 107],
            ['from' => 'JPY', 'to' => 'KRW', 'sell_rate' => 10.9, 'buy_rate' => 10.3],
        ];


        foreach ($rates as $rate) {
            // 取得對應的 currency_id
            $from_id = $currencies[$rate['from']] ?? null;
            $to_id = $currencies[$rate['to']] ?? null;
            if ($from_id === null || $to_id === null) {
                // 若有幣別查不到，跳過
                continue;
            }
            DB::table('rates')->insert([
                'from_currency_id' => $from_id,
                'to_currency_id' => $to_id,
                'sell_rate' => $rate['sell_rate'],
                'buy_rate' => $rate['buy_rate'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}

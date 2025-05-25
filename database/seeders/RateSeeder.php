<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rate;
use App\Models\CurrencyCode;

class RateSeeder extends Seeder
{
    public function run(): void
    {
        $currencies = CurrencyCode::pluck('id', 'code');

        $rates = [
            ['from' => 'USDT', 'to' => 'SGD', 'sell_rate' => 1.15, 'buy_rate' => 1.10],
            ['from' => 'USDT', 'to' => 'JPY', 'sell_rate' => 145,  'buy_rate' => 142],
            ['from' => 'USDT', 'to' => 'KRW', 'sell_rate' => 1418, 'buy_rate' => 1410],
            ['from' => 'SGD', 'to' => 'KRW', 'sell_rate' => 1100, 'buy_rate' => 1086],
            ['from' => 'SGD', 'to' => 'JPY', 'sell_rate' => 109,  'buy_rate' => 107],
            ['from' => 'JPY', 'to' => 'KRW', 'sell_rate' => 10.9, 'buy_rate' => 10.3],
        ];

        foreach ($rates as $r) {
            $fromId = $currencies[$r['from']] ?? null;
            $toId   = $currencies[$r['to']]   ?? null;

            if (! $fromId || ! $toId) {
                continue;
            }

            Rate::firstOrCreate(
                [
                    'from_currency_id' => $fromId,
                    'to_currency_id'   => $toId,
                ],
                [
                    'sell_rate' => $r['sell_rate'],
                    'buy_rate'  => $r['buy_rate'],
                ],
            );
        }
    }
}

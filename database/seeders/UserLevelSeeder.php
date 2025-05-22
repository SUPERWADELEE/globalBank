<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserLevel;

class UserLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (UserLevel::count() > 0) {
            return;
        }
        $levels = ['vip1', 'vip2', 'vip3', 'vip4', 'vip5', 'vip6', 'vip7', 'vip8', 'vip9', 'vip10'];
        foreach ($levels as $level) {
            UserLevel::create([
                'name' => $level,
            ]);
        }
    }
}

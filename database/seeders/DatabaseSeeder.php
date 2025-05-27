<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CurrencyCodeSeeder::class,
            RateSeeder::class,
            PlatformWalletSeeder::class,
            UserLevelSeeder::class,
            PermissionSeeder::class,
            FeeSeeder::class,
            UserPermissionsSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}

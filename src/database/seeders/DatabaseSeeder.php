<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 実行順序を制御できます
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            AttendanceSeeder::class,
            StampCorrectionRequestSeeder::class,
      ]);
    }
}

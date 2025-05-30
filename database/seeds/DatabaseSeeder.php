<?php

use Database\Seeders\BusinessSetting;
use Database\Seeders\BusinessSettingSeed;
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
        // $this->call(UsersTableSeeder::class);
        $this->call(BusinessSettingSeed::class);
    }
}

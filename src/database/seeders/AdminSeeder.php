<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\CategoryItem;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! Admin::exists()) Admin::factory(3)->create();
    }
}

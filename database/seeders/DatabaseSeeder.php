<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Bag;
use App\Models\Product;
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
        // \App\Models\User::factory(8)->create();
        // Bag::factory()->count(3)->create();
        // Product::factory(3)->create();
        \App\Models\Own::factory()->count(4)->create([
            'user_id' => random_int(1,8),
            'addProducts' => random_int(0,1),
            'authUser' => random_int(0,1),
            'authProducts' => random_int(0,1)
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents; # DO NOT USE THIS!!! This means Disable all model events globally until seeding is finished

	/**
	 * Seed the application's database.
	 */
    public function run(): void
    {
		// The order is important!
        $this->call([
			CategorySeeder::class,
			TagSeeder::class,
			PostSeeder::class
		]);
    }
}

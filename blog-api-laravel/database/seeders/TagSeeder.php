<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$tags = [
			'Laravel',
			'PHP',
			'JavaScript',
			'React',
			'Vue.js',
			'API',
			'Tutorial',
			'Web Development',
			'Mobile',
			'DevOps',
			'Database',
			'Security',
			'Performance',
			'Best Practices',
		];

		foreach($tags as $tag) {
			Tag::create(['name'=>$tag]);
		}
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$categories = [
			[
				'name' => 'Technology',
				'description' => 'Posts about technology, programming, and software development'
			],
			[
				'name' => 'Business',
				'description' => 'Business insights, entrepreneurship, and startups'
			],
			[
				'name' => 'Design',
				'description' => 'UI/UX design, graphic design, and creative work'
			],
			[
				'name' => 'Marketing',
				'description' => 'Digital marketing, SEO, and content strategy'
			],
			[
				'name' => 'Productivity',
				'description' => 'Tips and tools for better productivity'
			],
		];

		foreach($categories as $category) {
			Category::create($category);
		}
	}
}

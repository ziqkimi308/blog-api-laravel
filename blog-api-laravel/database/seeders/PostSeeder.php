<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// Create a user for test
		$user = User::create([
			'name' => 'John Blogger',
			'email' => 'john@blog.com',
			'password' => 'password123'
		]);

		// Fetch categories and tags
		$categories = Category::all();
		$tags = Tag::all();

		// Prepare posts
		$posts = [
			[
				'title' => 'Getting Started with Laravel 11',
				'content' => '<p>Laravel 11 brings exciting new features and improvements. In this comprehensive guide, we\'ll explore everything you need to know to get started with Laravel 11.</p><p>We\'ll cover installation, project setup, routing, controllers, and more. By the end of this tutorial, you\'ll have a solid foundation in Laravel development.</p>',
				'status' => 'published',
				'published_at' => now(), # this is very important!

				'category_id' => $categories->where('name', 'Technology')->first()->id,
			],
			[
				'title' => 'Building RESTful APIs with Laravel',
				'content' => '<p>REST APIs are the backbone of modern web applications. Laravel makes it incredibly easy to build robust, scalable APIs.</p><p>In this tutorial, we\'ll build a complete API from scratch, covering authentication, CRUD operations, relationships, and best practices.</p>',
				'status' => 'published',
				'published_at' => now(),

				'category_id' => $categories->where('name', 'Technology')->first()->id,
			],
			[
				'title' => 'The Future of Web Development',
				'content' => '<p>Web development is evolving rapidly. From AI integration to edge computing, the landscape is changing fast.</p><p>Let\'s explore the trends that will shape web development in the coming years and how you can prepare for them.</p>',
				'status' => 'published',
				'published_at' => now(),

				'category_id' => $categories->where('name', 'Technology')->first()->id,
			],
			[
				'title' => 'Starting Your Tech Startup',
				'content' => '<p>Launching a tech startup is challenging but rewarding. Here\'s everything I learned from building my first SaaS product.</p><p>We\'ll cover idea validation, MVP development, finding your first customers, and scaling your business.</p>',
				'status' => 'published',
				'published_at'=>now(),
				'category_id' => $categories->where('name', 'Business')->first()->id,
			],
			[
				'title' => 'Draft: My Upcoming Article',
				'content' => '<p>This is a draft post that only the author can see.</p>',
				'status' => 'draft',
				// 'published_at' => now(), # draft no need this
				'category_id' => $categories->where('name', 'Technology')->first()->id,
			],
		];

		foreach ($posts as $postData) {
			$post = $user->posts()->create($postData);

			// Attach random tags
			$randomTags = $tags->random(rand(2, 4))->pluck('id'); # random() and pluck() are Laravel collection method while rand() is PHP standard function.
			$post->tags()->attach($randomTags); # attach is like "append" version of sync (replace all)
		}
	}
}

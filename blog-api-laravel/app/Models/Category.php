<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
	use HasFactory;

	// mass-assignment
	protected $fillable = [
		'name',
		'slug',
		'description'
	];


	// foreign relation
	public function posts()
	{
		return $this->hasMany(Post::class);
	}

	// Event Listener / ORM Lifecycle
	protected static function booted()
	{
		// The slug is never intended to be custom
		
		// Laravel automatically passes the model instance being created into that callback
		static::creating(function ($category) {
			if (empty($category->slug)) {
				// Str::slug() converts string into "my-name-is" form
				$category->slug = Str::slug($category->name);
			}
		});

		static::updating(function ($category) {
			// If category changes, update slug
			if ($category->isDirty('name')) {
				$category->slug = Str::slug($category->name);
			}
		});
	}
}

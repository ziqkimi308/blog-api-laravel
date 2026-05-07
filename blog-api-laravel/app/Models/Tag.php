<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

	protected $fillable = [
		'name', 'slug'
	];

	// relationship
	public function posts()
	{
		return $this->belongsTo(Post::class);
	}

	// Lifecycle / Event
	public static function booted()
	{
		static::creating(function ($tag) {
			if (empty($tag->slug)) {
				$tag->slug = Str::slug($tag->name);
			}
		});

		static::updating(function ($tag) {
			if ($tag->isDirty('name')) {
				$tag->slug = Str::slug($tag->name);
			}
		});
	}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'user_id',
		'category_id',
		'title',
		'slug',
		'excerpt',
		'content',
		'featured_image',
		'status',
		'published_at',
		'views'
	];

	protected $casts = [
		'published_at' => 'datetime'
	];

	// appends always mean to be paired with an accessor eg; getReadingTimeAttribute()
	protected $appends = [
		'reading_time'
	];

	// relationship
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function tags()
	{
		// When you define a belongsToMany relationship, Laravel uses a pivot table
		// If you add ->withTimestamps(), Laravel will also automatically maintain the created_at and updated_at columns on the pivot table
		return $this->belongsToMany(Tag::class)->withTimestamps();
	}

	// Scopes
	public function scopePublished($query)
	{
		return $query->where('status', 'published')->where('published_at', '<=', now());
	}

	public function scopeDraft($query)
	{
		return $query->where('status','draft');
	}

	// Accessor
	public function getReadingTimeAttribute()
	{
		$wordCount = str_word_count(strip_tags($this->content));
		$minutes = ceil($wordCount / 200);
		return $minutes . ' min read';
	}

	public function getFeaturedImageUrlAttribute()
	{
		if ($this->featured_image) {
			// asset() generate a full URL
			return asset('storage/' . $this->featured_image);
		}
		return null;
	}

	// Lifecycle events
	public static function booted()
	{
		static::creating(function ($post) {
			// Assign slug if not provided
			if (empty($post->slug)) {
				$slug = Str::slug($post->title);
				$count = 1;

				// Check if other post has same slug to ensure uniqueness
				while (Post::where('slug', $slug)->exists()) {
					$slug = Str::slug($post->title) . '-' . $count;
					$count++;
				}

				$post->slug = $slug;
			}

			// Assign excerpt if not provided
			// excerpt is basically a short summary or preview of a longer piece of text
			if (empty($post->excerpt)) {
				// strip_tags remove all html tags
				$post->excerpt = Str::limit(strip_tags($post->content), 150);
			}

			// Assign published_at if publishing
			if ($post->status === 'published' && !$post->published_at) {
				$post->published_at = now();
			}
		});

		static::updating(function ($post) {
			// Update slug if title updated
			if ($post->isDirty('title')) {
				$slug = Str::slug($post->title);
				$count = 1;

				// Check if slug unique
				while (Post::where('slug',$slug)->where('id','!=',$post->id)->exists()) {
					$slug = Str::slug($post->title) . '-' . $count;
					$count++;
				}
				$post->slug = $slug;
			}

			// When change status to published, update the published_at
			if ($post->isDirty('status' && $post->status === 'published' && !$post->published_at)) {
				$post->published_at = now();
			}
		});
	}
}

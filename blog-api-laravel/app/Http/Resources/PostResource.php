<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'title' => $this->title,
			'slug' => $this->slug,
			'excerpt' => $this->excerpt,
			'content' => $this->when($request->routeIs('posts.show'), $this->content),
			'featured_image' => $this->featured_image,
			'featured_image_url' => $this->featured_image_url,
			'status' => $this->status,
			'published_at' => $this->published_at?->toDateTimeString(),
			'views' => $this->views,
			'reading_time' => $this->reading_time,
			'author' => new UserResource($this->whenLoaded('user')),
			'category' => new CategoryResource($this->whenLoaded('category')),
			'tags' => TagResource::collection($this->whenLoaded('tags')),
			'created_at' => $this->created_at->toDateTimeString(),
			'updated_at' => $this->updated_at->toDateTimeString(),
		];
	}
}

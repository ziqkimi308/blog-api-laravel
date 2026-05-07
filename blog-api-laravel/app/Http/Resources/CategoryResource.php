<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request):array
	{
		return [
			'id'=>$this->id,
			'name'=>$this->name,
			'slug' => $this->slug,
			'description' => $this->description,
			'posts_count' => $this->whenLoaded('posts', function () {
				return $this->posts->count();
			}),
			'created_at' => $this->created_at->toDateTimeString(),
		];
	}
}

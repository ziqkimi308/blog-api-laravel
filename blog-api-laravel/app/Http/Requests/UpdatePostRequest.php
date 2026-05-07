<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
		return [
			'title' => 'sometimes|string|max:255',
			'content' => 'sometimes|string',
			'excerpt' => 'nullable|string|max:500',
			'category_id' => 'nullable|exists:categories,id',
			'tags' => 'nullable|array',
			'tags.*' => 'exists:tags,id',
			'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
			'status' => 'sometimes|in:draft,published'
		];
    }
}

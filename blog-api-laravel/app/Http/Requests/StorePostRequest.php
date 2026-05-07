<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'title'=>'required|string|max:255',
			'content'=>'required|string',
			'excerpt'=>'nullable|string|max:500',
			'category_id'=>'nullable|exists:categories,id',
			'tags'=>'nullable|array',
			'tags.*'=>'exists:tags,id',
			'featured_image'=>'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
			'status'=>'in:draft,published'
        ];
    }

	// Custom validation messages
	public function messages():array
	{
		return [
			'featured_image.image'=>'The file must be an image',
			'featured_image.max'=>'The image must not be larger than 2MB'
		];
	}
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all query
		$categories = Category::withCount('posts')->get();

		// Response
		return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate data format
		$validated = $request->validate([
			'name'=>'required|string|max:255|unique:categories,name',
			'description'=>'nullable|string'
		]);

		// Create query
		$category = Category::create($validated);

		// Response
		return response()->json([
			'success'=>true,
			'message'=>'Category created successfully',
			'data'=>new CategoryResource($category)
		], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
		// Eager load the post count
        $category->loadCount('posts');

		// response
		return response()->json([
			'success'=>true,
			'data'=>new CategoryResource($category)
		]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
		// Validate data format
		$validated = $request->validate([
			'name' => ['sometimes|string|max:255',Rule::unique('categories','name')->ignore($category->id)],
			'description' => 'nullable|string'
		]);

		// Update query
		$category->update($validated);

		// Response
		return response()->json([
			'success'=>true,
			'message'=>'Category updated successfully',
			'data'=>new CategoryResource($category)
		]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Delete query
		$category->delete();

		// Response
		return response()->json([
			'success'=>true,
			'message'=>'Category deleted successfully'
		]);
    }
}

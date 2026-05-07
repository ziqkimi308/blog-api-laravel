<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
		// Fetch query
		$tags = Tag::withCount('posts')->get();

		// Response
		return TagResource::collection($tags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
		// Validate data format
		$validated = $request->validate([
			'name' => 'required|string|max:255|unique:tags,name'
		]);

		// Create query
		$tag = Tag::create($validated);

		// Response
		return response()->json([
			'success' => true,
			'message' => 'Tag created successfully',
			'data' => new TagResource($tag)
		], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
		// Fetch query
		$tag->loadCount('posts');

		// Response
		return response()->json([
			'success' => true,
			'data' => new TagResource($tag)
		]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
		// Validate data format
		$validated = $request->validate([
			'name' => 'sometimes|string|max:255|unique:tags,name,' . $tag->id
		]);

		// Update query
		$tag->update($validated);

		// Response
		return response()->json([
			'success' => true,
			'message' => 'Tag updated successfully',
			'data' => new TagResource($tag)
		]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
		// Delete query
		$tag->delete();

		// Response
		return response()->json([
			'success' => true,
			'message' => 'Tag deleted successfully'
		]);
    }
}

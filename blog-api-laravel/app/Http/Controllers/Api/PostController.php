<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		// Fetch all query
		$query = Post::with(['user', 'category', 'tags']);

		// Filter by status
		// If user not login (not authenticated), shows published post only
		if (!$request->user()) {
			$query->published();
		} else {
			// If user filter by status
			if ($request->has('status')) {
				// If status draft, show only draft post of current user
				if ($request->status === 'draft') {
					$query->where('user_id', $request->user()->id)
						->where('status', 'draft');
				} else {
					// If status published, show all published posts
					$query->where('status', $request->input('status'));
				}
			}
		}

		// Filter by category
		if ($request->has('category')) {
			// whereHas is has with condition
			$query->whereHas('category', function ($query) use ($request) {
				// slug is a column inside category table
				// so we did not filter by category's name but category's slug
				$query->where('slug', $request->input('category'));
			});
		}

		// Filter by tag
		if ($request->has('tag')) {
			$query->whereHas('tags', function ($query) use ($request) {
				$query->where('slug', $request->input('tag'));
			});
		}

		// Filter by search
		if ($request->has('search')) {
			$search = $request->input('search');
			$query->where(function ($query) use ($search) {
				$query->where('title', 'ilike', "%{$search}%")
					->orWhere('content', 'ilike', "%{$search}%")
					->orWhere('excerpt', 'ilike', "%{$search}%");
			});
		}

		// Sort
		$sortBy = $request->input('sort_by', 'published_at');
		$sortOrder = $request->input('sort_order', 'desc');

		if ($sortBy === 'popular') {
			$query->orderBy('views', 'desc');
		} else {
			$query->orderBy($sortBy, $sortOrder);
		}

		// Pagination
		$perPage = $request->input('per_page', 15);
		$posts = $query->paginate($perPage)->withQueryString();

		// Response
		return PostResource::collection($posts);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	private function uploadImage($file)
	{
		// Generate unique filename
		$filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

		// storeAs() moves file from temporary folder into storage/app/public/posts
		// and return the relative path
		$path = $file->storeAs('posts', $filename, 'public');

		return $path;
	}

	public function store(StorePostRequest $request)
	{
		// Validate data format
		$validated = $request->validated();

		// Handle image upload
		if ($request->hasFile('featured_image')) {
			// $validated['featured_image'] does not contain image path yet at this stage. So we assign one.
			$validated['featured_image'] = $this->uploadImage($request->file('featured_image'));
		}

		// Extract tags from validated
		$tags = $validated['tags'] ?? [];
		unset($validated['tags']);

		// Create post query
		$validated['user_id'] = $request->user()->id;
		$post = Post::create($validated);

		// Attach tags (special for many-to-many relationship)
		if (!empty($tags)) {
			$post->tags()->attach($tags);
		}

		// Eager load
		$post->load(['user', 'category', 'tags']);

		// Response
		return response()->json([
			'success' => true,
			'message' => 'Post created successfully',
			'data' => new PostResource($post)
		], 201);
	}

	/**
	 * Display the specified resource.
	 */
	// The slug is pass here instead of id (route binding model)
	// To make the url human SEO friendly
	public function show(Request $request, $slug)
	{
		// Fetch specific query
		$post = Post::where('slug', $slug)->with(['user', 'category', 'tags'])
			->firstOrFail(); // get() returns collection while first() return single

		if ($post->status === 'draft') {
			if (!$request->user() || $post->user_id !== $request->user()->id) {
				return response()->json([
					'success' => false,
					'message' => 'Post not found'
				], 404);
			}
		} else {
			// views is column in post table
			$post->increment('views');
		}

		// Response
		return response()->json([
			'success' => true,
			'data' => new PostResource($post)
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdatePostRequest $request, Post $post)
	{
		// User authorization
		if ($post->user_id !== $request->user()->id) {
			return response()->json([
				'success' => false,
				'message' => 'Unauthorized'
			], 403);
		}

		// Validate data format
		$validated = $request->validated();

		// Handle new image upload
		if ($request->hasFile('featured_image')) {
			// Delete old image
			if ($post->has('featured_image')) {
				Storage::disk('public')->delete($post->featured_image);
			}
			// Update image in $validated
			$validated['featured_image'] = $this->uploadImage($request->file('featured_image'));
		}

		// Unset tags
		$tags = $validated['tags'] ?? null;
		unset($validated['tags']);

		// Update post query
		$post->update($validated);

		// Sync tags
		if ($tags !== null) {
			$post->tags()->sync($tags);
		}

		// No need eager loading if we gonna fetch fresh() anyway
		// $post->load(['user', 'category', 'tags']);

		return response()->json([
			'success' => true,
			'message' => 'Post updated successfully',
			'data' => new PostResource($post->fresh(['user', 'category', 'tags']))
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Request $request, Post $post)
	{
		// User authorization
		if ($post->user_id !== $request->user()->id) {
			return response()->json([
				'success' => false,
				'message' => 'Unauthorized'
			], 403);
		}

		// Delete image
		if ($post->has('featured_image')) {
			Storage::disk('public')->delete($post->featured_image);
		}

		// Delete post query
		$post->delete();

		// Response
		return response()->json([
			'success' => true,
			'message' => 'Post deleted successfully'
		]);
	}

	// Delete featured image
	public function deleteImage(Request $request, Post $post)
	{
		// User authorization
		if ($request->user()->id !== $post->user_id) {
			return response()->json([
				'success' => false,
				'message' => 'Unauthorized'
			], 403);
		}

		// Data format validation
		if (!$post->featured_image) {
			return response()->json([
				'success' => false,
				'message' => 'No image to delete'
			], 400);
		}

		// Delete image query
		Storage::disk('public')->delete($post->featured_image);

		// Remove image data from table
		$post->update(['featured_image' => null]);

		// Response 
		return response()->json([
			'success' => true,
			'message' => 'Image deleted successfully'
		]);
	}

	// Fetch user's own posts
	public function myPosts(Request $request)
	{
		// Fetch posts query
		$query = $request->user()->posts()
			->with(['category', 'tags']);

		// Filter by status
		if ($request->has('status')) {
			$query->where('status', $request->input('status'));
		}

		// Filter by order by and pagination
		$posts = $query->orderBy('created_at', 'desc')->paginate(15);

		// Response
		return PostResource::collection($posts);
	}

	// Publish a draft
	public function publish(Request $request, Post $post)
	{
		// Authorization
		if ($post->user_id !== $request->user()->id) {
			return response()->json([
				'success' => false,
				'message' => 'Unauthorized'
			], 403);
		}

		// Update status query
		$post->update([
			'status' => 'published',
			'published_at' => now()
		]);

		// Response
		return response()->json([
			'success' => true,
			'message' => 'Post published successfully',
			'data' => new PostResource($post->fresh(['user', 'category', 'tags']))
		]);
	}

	// Unpublish a post (published to draft)
	public function unpublish(Request $request, Post $post)
	{
		// Authorization
		if ($post->user_id !== $request->user()->id) {
			return response()->json([
				'success' => false,
				'message' => 'Unauthorized'
			], 403);
		}

		// Update status query
		$post->update(['status' => 'draft']);

		// Response
		return response()->json([
			'success' => true,
			'message' => 'Post unpublished successfully',
			'data' => new PostResource($post->fresh(['user', 'category', 'tags']))
		]);
	}
}

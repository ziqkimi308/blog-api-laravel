<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Fetch all query
		$query = Post::with(['user', 'category', 'tags']);

		// For non-authenticated users, show only published posts
		if (!$request->user()) {
			$query->published();
		} else {
			// Authenticated and Author
			if ($request->has('status')) {
				$query->where('status', $request->status);
			} else {
				// Default: show all
				$query->where(function ($query) use ($request) {
					$query->where('user_id', $request->user()->id)
						->orWhere('status', 'published');
				});
			}
		}
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

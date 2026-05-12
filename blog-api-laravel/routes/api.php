<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public
Route::prefix('v1')->group(function () {
	// Auth
	Route::post('/register', [AuthController::class, 'register']);
	Route::post('/login', [AuthController::class, 'login']);

	// Fetch post
	Route::get('/posts', [PostController::class, 'index']);
	Route::get('/posts/{slug}', [PostController::class, 'show']);

	// Fetch categories
	Route::get('/categories', [CategoryController::class, 'index']);
	Route::get('/categories/{category}', [CategoryController::class, 'show']);

	// Fetch tags
	Route::get('/tags', [TagController::class, 'index']);
	Route::get('/tags/{tag}', [TagController::class, 'show']);
});


// Protected
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
	// Auth
	Route::post('/logout', [AuthController::class, 'logout']);
	Route::get('/me', [AuthController::class, 'me']);

	// Posts
	Route::get('/my-posts', [PostController::class, 'myPosts']);
	Route::post('/posts', [PostController::class, 'store']);
	Route::put('/posts/{post}', [PostController::class, 'update']);
	Route::delete('/posts/{post}', [PostController::class, 'destroy']);

	// Posts - Publish and Unpublish Status
	Route::post('/posts/{post}/publish', [PostController::class, 'publish']);
	Route::post('/posts/{post}/unpublish', [PostController::class, 'unpublish']);

	// Post - Delete Featured Image
	Route::delete('/posts/{post}/image', [PostController::class, 'deleteImage']);

	// Categories - (admin only - simplify for now)
	Route::post('/categories', [CategoryController::class, 'store']);
	Route::put('/categories/{category}', [CategoryController::class, 'update']);
	Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

	// Tags - (admin only - simplify for now)
	Route::post('/tags', [TagController::class, 'store']);
	Route::put('/tags/{tag}', [TagController::class, 'update']);
	Route::delete('/tags/{tag}', [TagController::class, 'destroy']);
});

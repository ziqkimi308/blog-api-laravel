<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
	// Register user
	public function register(Request $request)
	{
		// Validate data (Can be requestForm)
		$validated = $request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|email|unique:users,email',
			'password' => 'required|string|min:6|confirmed'
		]);

		// Create user query
		$user = User::create($validated);

		// Generate token
		$token = $user->createToken('auth_token')->plainTextToken;

		// Response
		return response()->json([
			'success' => true,
			'message' => 'Registration successful',
			'user' => new UserResource($user),
			'token' => $token
		], 201);
	}

	// Login user
	public function login(Request $request)
	{
		// Validate data (Can be requestForm)
		$validated = $request->validate([
			'email' => 'required|email',
			'password' => 'required'
		]);

		// login query - verify query
		$user = User::where('email', $validated["email"])->first();
		if (!$user || !Hash::check($validated["password"], $user->password)) {
			throw ValidationException::withMessages([
				'email' => ['The provided credentials are incorrect.'],
			]);
		}

		// Delete all tokens and create new token
		$user->tokens()->delete();
		$token = $user->createToken('auth_token')->plainTextToken;

		// Response
		return response()->json([
			'success' => 'true',
			'message' => 'Login successful',
			'user' => new UserResource($user),
			'token' => $token
		]);
	}

	// Logout user
	public function logout(Request $request)
	{
		// Delete current token
		$request->user()->currentAccessToken()->delete();

		// Response
		return response()->json([
			'success'=>true,
			'message'=>'Logged out successful'
		]);
	}

	// Return user stats
	public function me(Request $request)
	{
		return response()->json([
			'success'=>true,
			'user'=>new UserResource($request->user())
		]);
	}
}

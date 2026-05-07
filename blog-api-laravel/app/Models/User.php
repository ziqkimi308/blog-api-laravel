<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    // fillable
	protected $fillable = [
		'name',
		'email',
		'password'
	];

	// hidden
	protected $hidden = [
		'password',
		'remember_token'
	];

	// casts
	protected $casts = [
		'email_verified_at'=>'datetime',
		'password'=>'hashed'
	];

	// foreign relation
	public function posts()
	{
		return $this->hasMany(Post::class);
	}
}

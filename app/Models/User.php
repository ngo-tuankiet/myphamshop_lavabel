<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $timestamps = false;

    public const ROLE_USER = 1;
    public const ROLE_ADMIN = 2;

    protected $fillable = [
        'username',
        'name',
        'fullname',
        'email',
        'password',
        'email_verified_at',
        'role',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function favourites()
    {
        return $this->belongsToMany(Product::class, 'favourites', 'user_id', 'product_id');
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

}

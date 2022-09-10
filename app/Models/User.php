<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    protected $fillable = [
        'name',
        'lastname',
        'phone',
        'code',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
    ];

    public function own() {
        return $this->hasOne(Own::class);
    }
    public function images() {
        return $this->morphMany(images::class, 'image');
    }
    public function bag() {
        return $this->hasOne(Bag::class);
    }
    public function storeUserRelation() {
        return $this->hasMany(storeUserRelation::class, 'user_id');
    }
    public function user_geter() {
        return $this->hasMany(Store_ditail::class, "user_geter_id");
    }
    public function user_sender() {
        return $this->hasMany(Store_ditail::class, "user_sender_id");
    }
    public function userCreator() {
    	return $this->hasMany(Product::class, 'user_creator');
    }
}

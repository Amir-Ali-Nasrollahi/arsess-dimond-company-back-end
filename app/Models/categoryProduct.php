<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoryProduct extends Model
{
	protected $fillable = ['name', 'created_at', 'updated_at'];
    use HasFactory;
	public function products() {
		return $this->hasMany(Product::class,'category_id');
	}
}

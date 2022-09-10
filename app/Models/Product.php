<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use HasFactory;
	protected $fillable = ['store_id'];
        use SoftDeletes;
    public function images() {
        return $this->morphMany(images::class,'image');
    }
    public function ditails() {
        return $this->hasMany(Ditail::class,"product_id");
    }
    public function store_ditails() {
        return $this->hasMany(Store_ditail::class, "product_id");
    }
    public function category() {
    	return $this->belongsTo(categoryProduct::class,'category_id');
    }
    public function userCreator() {
	    return $this->belongsTo(User::class, 'user_creator');
    }
    public function describe() {
	    return $this->morphMany(Describe::class, 'describe');
    }
    public function store() {
        return $this->belongsTo(Store::class, 'store_id');
    }
}

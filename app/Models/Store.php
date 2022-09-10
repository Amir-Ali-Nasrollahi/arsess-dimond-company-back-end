<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    public function store_ditails_sender() {
        return $this->hasMany(Store_ditail::class, "store_sender_id");
    }
    public function store_ditails_geter() {
        return $this->hasMany(Store_ditail::class, "store_geter_id");
    }
    public function storeUserRelation() {
        return $this->hasMany('store_id');
    }
    public function products() {
        return $this->hasMany(Product::class, 'store_id');
    }
}

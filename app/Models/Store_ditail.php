<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store_ditail extends Model
{
    use HasFactory;
    protected $fillable = ['store_sender_id', 'store_geter_id', 'user_sender_id', 'user_geter_id', 'product_id', 'getProduct'];
    public function user_send() {
        return $this->belongsTo(User::class, "user_sender_id");
    }
    public function user_get() {
        return $this->belongsTo(User::class, "user_geter_id");
    }
    public function store_send() {
        return $this->belongsTo(Store::class, "store_sender_id");
    }
    public function store_get() {
        return $this->belongsTo(User::class, "store_geter_id");
    }
    public function product() {
        return $this->belongsTo(Product::class, "product_id");
    }
}

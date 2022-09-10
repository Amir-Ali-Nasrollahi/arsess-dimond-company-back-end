<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class storeUserRelation extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'store_id'];
    public function users() {
	    return $this->belongsTo(User::class, 'user_id');
    }
    public function stores() {
	    return $this->belongsTo(Store::class, 'store_id');
    }
}

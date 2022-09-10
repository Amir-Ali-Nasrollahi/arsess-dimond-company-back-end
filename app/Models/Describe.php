<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Describe extends Model
{
    use HasFactory;
    protected $fillable = ['user_sender', "user_geter", "status", "store_sender", "store_geter", 'describe_type', 'describe_id'];
    public function describe() {
	    return $this->morphTo();
    }
}

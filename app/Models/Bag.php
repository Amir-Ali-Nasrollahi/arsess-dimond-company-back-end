<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bag extends Model
{
    use HasFactory;

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function ditails() {
        return $this->hasMany(Ditail::class, "bag_id");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ditail extends Model
{
    use HasFactory;

    public function bag() {
        return $this->belongsTo(Bag::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}

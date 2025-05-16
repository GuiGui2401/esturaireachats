<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImageHash extends Model
{
    protected $fillable = ['product_id', 'image_path', 'image_hash'];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

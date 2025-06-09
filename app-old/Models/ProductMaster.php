<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMaster extends Model
{
    public function category()
{
    return $this->belongsTo(ProductCategory::class, 'category_id');
}
public function stocks()
{
    return $this->hasMany(ProductStock::class, 'product_id');
}
}

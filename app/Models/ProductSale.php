<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSale extends Model
{
       public function customer()
{
    return $this->belongsTo(Customer::class, 'customer_account_number','account_number');
}
       public function category()
{
    return $this->belongsTo(ProductCategory::class, 'category_id','id');
}

       public function product()
{
    return $this->belongsTo(ProductMaster::class, 'category_id','id');
}
}

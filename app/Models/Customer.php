<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Customer extends Model
{
    use HasFactory;

   // Customer.php
public function productSales()
{
    return $this->hasMany(ProductSale::class, 'customer_account_number', 'account_number');
}
}

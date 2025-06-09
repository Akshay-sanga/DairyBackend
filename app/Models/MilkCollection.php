<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilkCollection extends Model
{
        public function customer()
{
    return $this->belongsTo(Customer::class, 'customer_account_number','account_number');
}
}

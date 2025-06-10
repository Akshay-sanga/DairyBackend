<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyMilkSale extends Model
{
    public function customer()
    {
        return $this->bilongsTo(Customer::class ,'customer_account_number','account_number');
    }
}

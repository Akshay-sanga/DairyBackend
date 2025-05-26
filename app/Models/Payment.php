<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function headDairy()
    {
        return $this->belongsTo(HeadDairyMaster::class ,'head_dairy_id','id');
    }
        public function customer()
{
    return $this->belongsTo(Customer::class, 'customer_account_number','account_number');
}
}

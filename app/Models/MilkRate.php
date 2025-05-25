<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilkRate extends Model
{
    
    protected $fillable = [
        'admin_id','fat','snf_7_5','snf_7_6','snf_7_8','snf_7_9','snf_8_0','snf_8_1','snf_8_2', 'snf_8_3', 'snf_8_4', 'snf_8_5', 'snf_8_6', 'snf_8_7', 'snf_8_8', 'snf_8_9', 'snf_9_0'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnfChart extends Model
{
    protected $table = 'snf_chart'; // Singular table name

  protected $fillable = [
    'admin_id', 'fat', 'clr_22', 'clr_23', 'clr_24',
    'clr_25', 'clr_26', 'clr_27', 'clr_28', 'clr_29', 'clr_30'
];


}


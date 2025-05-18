<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Admin extends Authenticatable   // <-- Change here
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
    ];
}

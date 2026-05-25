<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Operateur extends Authenticatable
{
    use HasApiTokens;

    protected $primaryKey = 'operateur_id';

    protected $fillable = [
        'login',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

}
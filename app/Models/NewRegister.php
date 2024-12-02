<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewRegister extends Model
{
    protected $fillable = [
        'name', 'mobile','dob','gender','age','language',
    ];
}   

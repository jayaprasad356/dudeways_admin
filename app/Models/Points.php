<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Points extends Model
{
    protected $fillable = [
        'points',
        'discount_points',
        'offer_percent',
        'price',
        'datetime'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_holder_name',
        'account_number',
        'ifsc_code',
        'bank_name',
        'branch_name',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}

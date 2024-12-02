<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoiceVerifications extends Model
{
    use HasFactory;

    protected $table = 'voice_verifications';

    protected $fillable = [
        'user_id',
        'voice',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Interests extends Model
{
    use Notifiable;

    protected $table = 'interests';

    protected $fillable = [
        'interest_user_id', 'user_id', 'datetime','status',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}


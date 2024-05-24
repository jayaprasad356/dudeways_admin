<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Notifications extends Model
{
    use Notifiable;

    protected $table = 'notifications';

    protected $fillable = [
        'notify_user_id', 'user_id', 'datetime','message',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}


<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Trips extends Authenticatable
{
    use Notifiable;

    protected $guard = 'trips';

    protected $table = 'trips';


    protected $fillable = [
        'planning', 'from_location', 'to_location', 'meetup_location', 'from_date', 'to_date', 'trip_title','trip_description','user_id','trip_datetime','trip_status','trip_image', // Add 'mobile' to the fillable fields
    ];

    public function users()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function findForPassport($mobile)
    {
        return $this->where('mobile', $mobile)->first();
    }
    public function getFullname()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAvatar()
    {
        return 'https://www.gravatar.com/avatar/' . md5($this->email);
    }
}

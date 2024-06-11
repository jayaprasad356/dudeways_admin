<?php

namespace App\Http\Controllers;

use App\Models\Points;
use App\Models\Users;
use App\Models\Trips;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users_count = Users::count();
        $trips_count = Trips::count();
        $points_count = Points::count();

         // Count of pending trips
    $pending_trips_count = Trips::where('trip_status', 0)->count();

        return view('home', [
            'users_count' => $users_count,
            'trips_count' => $trips_count,
            'points_count' => $points_count,
            'pending_trips_count' => $pending_trips_count,
        ]);
    }
}

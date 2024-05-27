<?php

namespace App\Http\Controllers;

use App\Http\Requests\TripsStoreRequest;
use App\Models\Trips;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;





class TripsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


     public function index(Request $request)
     {
         $query = Trips::query()->with('users'); // Eager load the user relationship
     
         // Filter by user if user_id is provided
         if ($request->has('user_id')) {
             $user_id = $request->input('user_id');
             $query->where('user_id', $user_id);
         }
     
         $trips = $query->latest()->paginate(10); // Paginate the results
     
         $users = Users::all(); // Fetch all users for the filter dropdown
     
         return view('trips.index', compact('trips', 'users')); // Pass trips and users to the view
     }
     
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = Users::all(); // Fetch all trips
        return view('trips.create', compact('users')); // Pass trips to the view
    }
  


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  
    
    public function store(TripsStoreRequest $request)
    {
    
        $trips = Trips::create([
            'planning' => $request->planning,
            'from_location' => $request->from_location,
            'to_location' => $request->to_location,
            'meetup_location' => $request->meetup_location,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'name_of_your_trip' => $request->name_of_your_trip,
            'description_of_your_trip' => $request->description_of_your_trip,
            'user_id' => $request->user_id,
            'datetime' => now(),
            
        ]);
    
        if (!$trips) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating user.');
        }
    
        return redirect()->route('trips.index')->with('success', 'Success, New trips has been added successfully!');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Trips  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Trips $trips)
    {

    }

    public function user()
    {
        return $this->belongsTo(users::class);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Trips $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Trips $trips)
    {
        $users = Users::all(); // Fetch all shops
        return view('trips.edit', compact('trips', 'users'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Trips  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Trips $trips)

    {
        $trips->planning = $request->planning;
        $trips->from_location = $request->from_location;
        $trips->to_location = $request->to_location;
        $trips->meetup_location = $request->meetup_location;
        $trips->from_date = $request->from_date;
        $trips->to_date = $request->to_date;
        $trips->name_of_your_trip = $request->name_of_your_trip;
        $trips->description_of_your_trip = $request->description_of_your_trip;
        $trips->user_id = $request->user_id;
        $trips->datetime = now(); 


        if (!$trips->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the customer.');
        }
        return redirect()->route('trips.index')->with('success', 'Success, Trip has been updated.');
    }

    public function destroy(Trips $trips)
    {
        $trips->delete();

        return response()->json([
            'success' => true
        ]);
    }
}

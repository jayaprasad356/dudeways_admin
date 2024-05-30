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
    
        // Check if a file has been uploaded
   if ($request->hasFile('trip_image')) {
    $imageName = $request->file('trip_image')->getClientOriginalName(); // Get the original file name
    $imagePath = $request->file('trip_image')->storeAs('trips', $imageName);
} else {
    // Handle the case where no file has been uploaded
    $imagePath = null; // or provide a default image path
}
        $trips = Trips::create([
            'planning' => $request->planning,
            'from_location' => $request->from_location,
            'to_location' => $request->to_location,
            'meetup_location' => $request->meetup_location,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'trip_title' => $request->trip_title,
            'trip_description' => $request->trip_description,
            'user_id' => $request->user_id,
            'trip_image' => $imageName, // Save only the image name in the database
            'trip_datetime' => now(),
            
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
        $trips->trip_title = $request->trip_title;
        $trips->trip_description = $request->trip_description;
        $trips->user_id = $request->user_id;
        $trips->trip_status = $request->trip_status;
        $trips->trip_datetime = now(); 

        if ($request->hasFile('trip_image')) {
            $newImagePath = $request->file('trip_image')->store('trips', 'public');
            Storage::disk('public')->delete('trips/' . $trips->trip_image);
            $trips->trip_image = basename($newImagePath);
        }


        if (!$trips->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the customer.');
        }
        return redirect()->route('trips.index')->with('success', 'Success, Trip has been updated.');
    }

    public function destroy(Trips $trips)
    {

         // Check if the profile image exists and delete it
         if (Storage::disk('public')->exists('trips/' . $trips->trip_image)) {
            Storage::disk('public')->delete('trips/' . $trips->trip_image);
        }
        $trips->delete();

        return response()->json([
            'success' => true
        ]);
    }
}

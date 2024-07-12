<?php

namespace App\Http\Controllers;

use App\Http\Requests\TripsStoreRequest;
use App\Models\Trips;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Berkayk\OneSignal\OneSignalClient;

class TripsController extends Controller
{
    protected $oneSignalClient;

    public function __construct(OneSignalClient $oneSignalClient)
    {
        $this->oneSignalClient = $oneSignalClient;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function updateStatus(Request $request)
     {
         $tripIds = $request->input('trip_ids', []);
         $status = $request->input('status');
 
         foreach ($tripIds as $tripId) {
             $trip = Trips::find($tripId);
             if ($trip) {
                 $oldStatus = $trip->trip_status;
                 $trip->trip_status = $status;
                 $trip->trip_datetime = now();
                 $trip->save();
 
                 // Only send notifications if the status has changed
                 if ($oldStatus !== $status) {
                     if ($status == 1) {
                        $userId = $trip->user_id;

                         $this->sendNotificationToAllUsers($trip->user_id);
 
                         // Send notification to the user who posted the trip
                         $this->sendNotificationToUser(strval($userId));
                     }
                 }
             }
         }
 
         return response()->json(['success' => true]);
     }
 
     /**
      * Send notification to all users that a new trip has been posted.
      *
      * @param int $userId
      * @return void
      */
     protected function sendNotificationToAllUsers($userId)
     {
         $user = Users::find($userId);
         if ($user) {
             $message = $user->name . " posted a new trip";
             $this->oneSignalClient->sendNotificationToAll(
                 $message,
                 $url = null,
                 $data = null,
                 $buttons = null,
                 $schedule = null
             );
         }
     }
 
     /**
      * Send notification to the user that their trip has been approved.
      *
      * @param int $userId
      * @return void
      */
     protected function sendNotificationToUser($userId)
     {
             $message = "Your trip has been approved successfully";
             $this->oneSignalClient->sendNotificationToExternalUser(
                 $message,
                 $userId,
                 $url = null,
                 $data = null,
                 $buttons = null,
                 $schedule = null
             );
     }
     public function index(Request $request)
     {
         $query = Trips::query()->with('users');
     
         if ($request->has('user_id')) {
             $user_id = $request->input('user_id');
             $query->where('user_id', $user_id);
         }
     
         if ($request->has('trip_status')) {
             $trip_status = $request->input('trip_status');
             $query->where('trip_status', $trip_status);
         } else {
             // By default, fetch pending trips
             $query->where('trip_status', 0);
         }
     
         $trips = $query->latest()->paginate(10);
         $users = Users::all();
     
         return view('trips.index', compact('trips', 'users'));
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
            'trip_type' => $request->trip_type,
            'location' => $request->location,
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

        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'trip_type' => 'required|string',
            'location' => 'required|string',
            'trip_title' => 'required|string',
            'trip_description' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);
        
        $trips->trip_type = $request->trip_type;
        $trips->location = $request->location;
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
        return redirect()->route('trips.edit', $trips->id)->with('success', 'Success, Trip has been updated.');
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

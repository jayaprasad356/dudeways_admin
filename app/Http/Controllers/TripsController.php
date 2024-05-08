<?php

namespace App\Http\Controllers;

use App\Http\Requests\TripsStoreRequest;
use App\Models\Trips;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TripsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Trips::query();
    
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('planning', 'like', "%$search%")
                  ->orWhere('location', 'like', "%$search%");
        }
    
        if ($request->wantsJson()) {
            return response($query->get());
        }
    
        $trips = $query->latest()->paginate(10);
        return view('trips.index')->with('trips', $trips);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('trips.create');
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
            'location' => $request->location,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'name_of_your_trip' => $request->name_of_your_trip,
            'description_of_your_trip' => $request->description_of_your_trip,
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

    public function trips()
{
    return $this->belongsTo(Trips::class);
}
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Trips $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Trips $trips)
    {
        return view('trips.edit', compact('trips'));
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
        $trips->location = $request->location;
        $trips->from_date = $request->from_date;
        $trips->to_date = $request->to_date;
        $trips->name_of_your_trip = $request->name_of_your_trip;
        $trips->description_of_your_trip = $request->description_of_your_trip;


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

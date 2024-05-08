<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersStoreRequest;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Users::query();
        
        // Check if there's a search query
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('mobile', 'like', "%$search%")
                  ->orWhere('name', 'like', "%$search%")
                  ->orWhere('refer_code', 'like', "%$search%");
        }
        
        // Check if the request is AJAX
        if ($request->wantsJson()) {
            return response($query->get());
        }
        
        // Retrieve all users if there's no search query
        $users = $query->latest()->paginate(10);
        
        return view('users.index')->with('users', $users);
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function generateReferCode()
    {
        // Generate a random string
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
        shuffle($characters);
        $refer_code = implode('', array_slice($characters, 0, 6));
    
        // Check if the generated refer_code already exists in the database
        // If it does, regenerate the refer_code until it's unique
        while (Users::where('refer_code', $refer_code)->exists()) {
            shuffle($characters);
            $refer_code = implode('', array_slice($characters, 0, 6));
        }
    
        return $refer_code;
    }
    
    public function store(UsersStoreRequest $request)
    {
        // Generate a refer_code regardless of whether referred_by is provided or not
        $refer_code = $this->generateReferCode();
    
        // If referred_by is provided, check if it exists in the users table
        if ($request->filled('referred_by')) {
            $existingUser = Users::where('refer_code', $request->referred_by)->first();
    
            if (!$existingUser) {
                return redirect()->back()->with('error', 'Invalid referred_by. Please provide a valid refer code.');
            }
    
            // If the referred_by is correct, use it to generate a new refer_code
            $refer_code = $this->generateReferCode();
        }
    
        $imagePath = $request->file('profile')->store('users', 'public');
    
        $users = Users::create([
            'name' => $request->name,
            'age' => $request->age,
            'email' => $request->email,
            'address' => $request->address,
            'mobile' => $request->mobile,
            'gender' => $request->gender,
            'profession' => $request->profession,
            'refer_code' => $refer_code,
            'referred_by' => $request->referred_by,
            'profile' => basename($imagePath),
        ]);
    
        if (!$users) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating user.');
        }
    
        return redirect()->route('users.index')->with('success', 'Success, New user has been added successfully!');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Users  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Users $users)
    {

    }

    public function users()
{
    return $this->belongsTo(Users::class);
}
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Users $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Users $users)
    {
        return view('users.edit', compact('users'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Users  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Users $users)

    {
        $users->name = $request->name;
        $users->age= $request->age;
        $users->email = $request->email;
        $users->mobile = $request->mobile;
        $users->gender = $request->gender;
        $users->refer_code = $request->refer_code;
        $users->referred_by = $request->referred_by;
        $users->profession = $request->profession;

        if ($request->hasFile('profile')) {
            $newImagePath = $request->file('profile')->store('users', 'public');
            // Delete old image if it exists
            Storage::disk('public')->delete('users/' . $users->profile);
            $users->profile = basename($newImagePath);
        }

        if (!$users->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the customer.');
        }
        return redirect()->route('users.index')->with('success', 'Success, User has been updated.');
    }

    public function destroy(Users $users)
    {
        if (Storage::disk('public')->exists('users/' . $users->profile)) {
            Storage::disk('public')->delete('users/' . $users->profile);
        }
        $users->delete();

        return response()->json([
            'success' => true
        ]);
    }
}

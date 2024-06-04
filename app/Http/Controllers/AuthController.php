<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users; 
use App\Models\Chats; 
use App\Models\Trips;
use App\Models\Friends; 
use App\Models\Points; 
use App\Models\Notifications; 
use App\Models\Verifications; 
use Carbon\Carbon;

class AuthController extends Controller
{
 
    public function check_mobile(Request $request)
    {
        // Retrieve phone number from the request
        $mobile = $request->input('mobile');

        if (empty($mobile)) {
            $response['success'] = false;
            $response['message'] = 'mobile is empty.';
            return response()->json($response, 400);
        }
    
        // Remove non-numeric characters from the phone number
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
    
        // Check if the length of the phone number is not equal to 10
        if (strlen($mobile) !== 10) {
            $response['success'] = false;
            $response['message'] = "mobile number should be exactly 10 digits";
            return response()->json($response, 400);
        }
    
    
        // Check if a customer with the given phone number exists in the database
        $user = Users::where('mobile', $mobile)->first();
    
        // If customer not found, return failure response
        if (!$user) {
            $response['success'] = true;
            $response['registered'] = false;
            $response['message'] = 'mobile not registered.';
            return response()->json($response, 404);
        }

    // Image URL
    $imageUrl = asset('storage/app/public/users/' . $user->profile);
    $coverimageUrl = asset('storage/app/public/users/' . $user->cover_img);

    return response()->json([
        'success' => true,
        'registered' => true,
        'message' => 'Logged in successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'unique_name' => $user->unique_name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'age' => $user->age,
            'gender' => $user->gender,
            'state' => $user->state,
            'city' => $user->city,
            'profession' => $user->profession,
            'refer_code' => $user->refer_code,
            'referred_by' => $user->referred_by,
            'profile' => $imageUrl,
            'cover_img' => $coverimageUrl,
            'points' => $user->points,
            'verified' => $user->verified,
            'online_status' => $user->online_status,
            'last_seen' => Carbon::parse($user->last_seen)->format('Y-m-d H:i:s'),
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 200);
}

public function check_email(Request $request)
{
    // Retrieve phone number from the request
    $email = $request->input('email');

    if (empty($email)) {
        return response()->json([
            'success' => false,
            'message' => 'Email is empty.',
        ], 400);
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid email format.',
        ], 400);
    }

    // Check if a customer with the given phone number exists in the database
    $user = Users::where('email', $email)->first();

    // If customer not found, return failure response
    if (!$user) {
        $response['success'] = true;
        $response['registered'] = false;
        $response['message'] = 'Email not registered.';
        return response()->json($response, 404);
    }

// Image URL
$imageUrl = asset('storage/app/public/users/' . $user->profile);
$coverimageUrl = asset('storage/app/public/users/' . $user->cover_img);

return response()->json([
    'success' => true,
    'registered' => true,
    'message' => 'Logged in successfully.',
    'data' => [
        'id' => $user->id,
        'name' => $user->name,
        'unique_name' => $user->unique_name,
        'email' => $user->email,
        'mobile' => $user->mobile,
        'age' => $user->age,
        'gender' => $user->gender,
        'state' => $user->state,
        'city' => $user->city,
        'profession' => $user->profession,
        'refer_code' => $user->refer_code,
        'referred_by' => $user->referred_by,
        'profile' => $imageUrl,
        'cover_img' => $coverimageUrl,
        'points' => $user->points,
        'verified' => $user->verified,
        'online_status' => $user->online_status,
        'last_seen' => Carbon::parse($user->last_seen)->format('Y-m-d H:i:s'),
        'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
        'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
    ],
], 200);
}

public function register(Request $request)
{
    $age = $request->input('age');
    $name = $request->input('name');
    $unique_name = $request->input('unique_name');
    $email = $request->input('email');
    $gender = $request->input('gender');
    $state = $request->input('state');
    $city = $request->input('city');
    $profession = $request->input('profession');
    $referred_by = $request->input('referred_by');
    $points = $request->input('points', 10);

    if (empty($state)) {
        return response()->json([
            'success' => false,
            'message' => 'state is empty.',
        ], 400);
    }
    if (empty($city)) {
        return response()->json([
            'success' => false,
            'message' => 'city is empty.',
        ], 400);
    }
    if (empty($age)) {
        return response()->json([
            'success' => false,
            'message' => 'Age is empty.',
        ], 400);
    } elseif ($age < 18 || $age > 60) {
        return response()->json([
            'success' => false,
            'message' => 'Age should be between 18 and 60.',
        ], 400);
    }

    if (empty($name)) {
        return response()->json([
            'success' => false,
            'message' => 'Name is empty.',
        ], 400);
    } elseif (strlen($name) < 4 || strlen($name) > 18) {
        return response()->json([
            'success' => false,
            'message' => 'Name should be between 4 and 18 characters.',
        ], 400);
    }

    if (empty($gender)) {
        return response()->json([
            'success' => false,
            'message' => 'Gender is empty.',
        ], 400);
    } 
    if (empty($profession)) {
        return response()->json([
            'success' => false,
            'message' => 'Profession is empty.',
        ], 400);
    }

    if (empty($email)) {
        return response()->json([
            'success' => false,
            'message' => 'Email is empty.',
        ], 400);
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid email format.',
        ], 400);
    }

    $existingUser = Users::where('unique_name', $unique_name)->first();
    if ($existingUser) {
        return response()->json([
            'success' => false,
            'message' => 'User already exists with this Unique Name.',
        ], 409);
    }

    // Check if the user with the given email already exists
    $existingEmail = Users::where('email', $email)->first();
    if ($existingEmail) {
        return response()->json([
            'success' => false,
            'message' => 'User already exists with this email.',
        ], 409);
    }
    // Generate a refer_code automatically
    $refer_code = $this->generateReferCode();

    // If referred_by is provided, validate it
    if (!empty($referred_by)) {
        $validReferredBy = Users::where('refer_code', $referred_by)->exists();
        if (!$validReferredBy) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid referred_by.',
            ], 400);
        }
    }

    $user = new Users();
    $user->age = $age;
    $user->name = $name;
    $user->gender = $gender;
    $user->profession = $profession;
    $user->refer_code = $this->generateReferCode();
    $user->email = $email;
    $user->points = $points;
    $user->state = $state;
    $user->city = $city;
    $user->referred_by = $referred_by;
    $user->datetime = now(); 
    $user->last_seen = now(); 
    // Save the user
    $user->save();

    // Retrieve the user's id
    $user_id = $user->id;

    // Generate unique_name based on name and user's id
    $unique_name = $this->generateUniqueName($name, $user_id);
    $user->unique_name = $unique_name;
    $user->save();

    // Image URL
    $imageUrl = asset('storage/app/public/users/' . $user->profile);
    $coverimageUrl = asset('storage/app/public/users/' . $user->cover_img);

    return response()->json([
        'success' => true,
        'message' => 'User registered successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'unique_name' => $user->unique_name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'age' => $user->age,
            'gender' => $user->gender,
            'state' => $user->state,
            'city' => $user->city,
            'profession' => $user->profession,
            'refer_code' => $refer_code, // Return the generated refer_code
            'referred_by' => $user->referred_by,
            'profile' => $imageUrl,
            'cover_img' => $coverimageUrl,
            'points' => $user->points,
            'verified' => 0,
            'online_status' => 0,
            'last_seen' => Carbon::parse($user->last_seen)->format('Y-m-d H:i:s'),
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 201);
}

private function generateUniqueName($name, $user_id)
{
    // Extract the first part of the user's name
    $parts = explode(' ', $name);
    $firstPart = $parts[0];

    // Generate the unique name by concatenating the first part with the user's id
    $unique_name = $firstPart . $user_id;

    // Check if the generated unique_name is already in use
    $counter = 1;
    while (Users::where('unique_name', $unique_name)->exists()) {
        // If it is, append a counter to make it unique
        $unique_name = $firstPart . $user_id . $counter;
        $counter++;
    }

    return $unique_name;
}
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

public function userdetails(Request $request)
{
$user_id = $request->input('user_id');

if (empty($user_id)) {
    return response()->json([
        'success' => false,
        'message' => 'user_id is empty.',
    ], 400);
}

// Fetch the customer details from the database based on the provided customer_id
$user = Users::find($user_id);

if (!$user) {
    return response()->json([
        'success' => false,
        'message' => 'user not found.',
    ], 404);
}

// Image URL
$imageUrl = asset('storage/app/public/users/' . $user->profile);
$coverimageUrl = asset('storage/app/public/users/' . $user->cover_img);

return response()->json([
    'success' => true,
    'message' => 'User details retrieved successfully.',
    'data' => [
        'id' => $user->id,
        'name' => $user->name,
        'unique_name' => $user->unique_name,
        'email' => $user->email,
        'mobile' => $user->mobile,
        'age' => $user->age,
        'gender' => $user->gender,
        'state' => $user->state,
        'city' => $user->city,
        'profession' => $user->profession,
        'refer_code' => $user->refer_code,
        'referred_by' => $user->referred_by,
        'profile' => $imageUrl,
        'cover_img' => $coverimageUrl,
        'points' => $user->points,
        'verified' => $user->verified,
        'online_status' => $user->online_status,
        'last_seen' => Carbon::parse($user->last_seen)->format('Y-m-d H:i:s'),
        'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
        'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
    ],
], 200);
}
public function update_image(Request $request)
{
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 404);
    }

    $profile = $request->file('profile');

    if ($profile !== null) {
        $imagePath = $profile->store('users', 'public');
        $user->profile = basename($imagePath);
        $user->datetime = now(); 
        $user->save();
        // Image URL
         $imageUrl = asset('storage/app/public/users/' . $user->profile);
         $coverimageUrl = asset('storage/app/public/users/' . $user->cover_img);

        return response()->json([
            'success' => true,
            'message' => 'User Profile updated successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'unique_name' => $user->unique_name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'age' => $user->age,
                'gender' => $user->gender,
                'state' => $user->state,
                'city' => $user->city,
                'profession' => $user->profession,
                'refer_code' => $user->refer_code,
                'referred_by' => $user->referred_by,
                'profile' => $imageUrl,
                'cover_img' => $coverimageUrl,
                'points' => $user->points,
                'verified' => $user->verified,
                'online_status' => $user->online_status,
                'last_seen' => Carbon::parse($user->last_seen)->format('Y-m-d H:i:s'),
                'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
            ],
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'profile image is empty.',
        ], 400);
    }
}


public function update_cover_img(Request $request)
{
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 404);
    }

    $cover_img = $request->file('cover_img');

    if ($cover_img !== null) {
        $imagePath = $cover_img->store('users', 'public');
        $user->cover_img = basename($imagePath);
        $user->datetime = now(); 
        $user->save();
        // Image URL
         $imageUrl = asset('storage/app/public/users/' . $user->profile);
         $coverimageUrl = asset('storage/app/public/users/' . $user->cover_img);

        return response()->json([
            'success' => true,
            'message' => 'Cover Image updated successfully For this User.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'unique_name' => $user->unique_name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'age' => $user->age,
                'gender' => $user->gender,
                'state' => $user->state,
                'city' => $user->city,
                'profession' => $user->profession,
                'refer_code' => $user->refer_code,
                'referred_by' => $user->referred_by,
                'profile' => $imageUrl,
                'cover_img' => $coverimageUrl,
                'points' => $user->points,
                'verified' => $user->verified,
                'online_status' => $user->online_status,
                'last_seen' => Carbon::parse($user->last_seen)->format('Y-m-d H:i:s'),
                'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
            ],
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Cover image is empty.',
        ], 400);
    }
}


public function update_users(Request $request)
{
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }
    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 404);
    }

    $name = $request->input('name');
    $email = $request->input('email');
    $unique_name = $request->input('unique_name');
    $age = $request->input('age');
    $profession = $request->input('profession');
    $state = $request->input('state');
    $city = $request->input('city');

if ($age !== null) {
    if ($age < 18 || $age > 60) {
        return response()->json([
            'success' => false,
            'message' => 'Age should be between 18 and 60.',
        ], 400);
    }
}

if ($name !== null) {
    if (strlen($name) < 4 || strlen($name) > 18) {
        return response()->json([
            'success' => false,
            'message' => 'Name should be between 4 and 18 characters.',
        ], 400);
    }
}

if ($email !== null) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid email format.',
        ], 400);
    }
}
    // Update offer details
    if ($name !== null) {
        $user->name = $name;
    }
    if ($email !== null) {
        $user->email = $email;
    }
    
    if ($age !== null) {
        $user->age = $age;
    }
   
    if ($profession !== null) {
        $user->profession = $profession;
    }
    if ($state !== null) {
        $user->state = $state;
    }
    if ($city !== null) {
        $user->city = $city;
    }
    if ($unique_name !== null) {
        $user->unique_name = $unique_name;
    }

    $user->datetime = now(); 

    $user->save();

    // Image URL
    $imageUrl = asset('storage/app/public/users/' . $user->profile);
    $coverimageUrl = asset('storage/app/public/users/' . $user->cover_img);

    return response()->json([
        'success' => true,
        'message' => 'User Details updated successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'unique_name' => $user->unique_name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'age' => $user->age,
            'gender' => $user->gender,
            'state' => $user->state,
            'city' => $user->city,
            'profession' => $user->profession,
            'refer_code' => $user->refer_code,
            'referred_by' => $user->referred_by,
            'profile' => $imageUrl,
            'cover_img' => $coverimageUrl,
            'points' => $user->points,
            'verified' => $user->verified,
            'online_status' => $user->online_status,
            'last_seen' => Carbon::parse($user->last_seen)->format('Y-m-d H:i:s'),
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 200);
}

public function add_trip(Request $request)
{
    $user_id = $request->input('user_id'); 
    $trip_type = $request->input('trip_type');
    $from_date = $request->input('from_date');
    $to_date = $request->input('to_date');
    $trip_title = $request->input('trip_title');
    $trip_description = $request->input('trip_description');
    $location = $request->input('location');

    $errors = [];

       // Validate each input and return specific error messages
       if (empty($trip_type)) {
        return response()->json([
            'success' => false,
            'message' => 'Trip Type is empty.',
        ], 400);
    }
    if (empty($from_date)) {
        return response()->json([
            'success' => false,
            'message' => 'From Date is empty.',
        ], 400);
    }
    if (empty($to_date)) {
        return response()->json([
            'success' => false,
            'message' => 'To Date is empty.',
        ], 400);
    }
    if (empty($trip_title)) {
        return response()->json([
            'success' => false,
            'message' => 'Trip Title is empty.',
        ], 400);
    }
    if (empty($trip_description)) {
        return response()->json([
            'success' => false,
            'message' => 'Trip Description is empty.',
        ], 400);
    }
    if (empty($location)) {
        return response()->json([
            'success' => false,
            'message' => 'Location is empty.',
        ], 400);
    }

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'User ID is empty.',
        ], 400);
    }
    // Check if the user exists
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 404);
    }

    // Check if any validation errors occurred
    if (!empty($errors)) {
        return response()->json([
            'success' => false,
            'message' => $errors,
        ], 400);
    }


    // Create a new user instance
    $trip = new trips();
    $trip->user_id = $user_id; 
    $trip->trip_type = $trip_type;
    $trip->from_date = $from_date;
    $trip->to_date = $to_date;
    $trip->trip_title = $trip_title;
    $trip->trip_description = $trip_description;
    $trip->location = $location;
    $trip->trip_datetime = now(); 
    $trip->save();

        // Image URL
        $imageUrl = asset('storage/app/public/trips/' . $trip->trip_image);
  // Fetch user details associated with the trip
  $user = Users::find($trip->user_id);


    return response()->json([
        'success' => true,
        'message' => 'Trip Added successfully.',
        'data' => [
            'id' => $trip->id,
            'name' => $user->name,
            'unique_name' => $user->unique_name,
            'verified' => $user->verified,
            'trip_type' => $trip->trip_type,
            'from_date' => date('F j, Y', strtotime($trip->from_date)),
            'to_date' => date('F j, Y', strtotime($trip->to_date)),
            'time' => '4h',
            'trip_title' => $trip->trip_title,
            'trip_description' => $trip->trip_description,
            'location' => $trip->location,
            'trip_status' => 0,
            'trip_image' => $imageUrl,
            'trip_datetime' => Carbon::parse($trip->trip_datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($trip->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($trip->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 201);
}

public function update_trip_image(Request $request)
{
    $tripId = $request->input('trip_id');

    if (empty($tripId)) {
        return response()->json([
            'success' => false,
            'message' => 'trip_id is empty.',
        ], 400);
    }

    $trip = Trips::find($tripId);

    if (!$trip) {
        return response()->json([
            'success' => false,
            'message' => 'Trip not found.',
        ], 404);
    }

    $tripImage = $request->file('trip_image');

    if ($tripImage) {
        $imagePath = $tripImage->store('trips', 'public');
        $trip->trip_image = basename($imagePath);
        $trip->trip_datetime = now(); 
        $trip->save();

        return response()->json([
            'success' => true,
            'message' => 'Trip image updated successfully.',
        ], 200);
    } 
    else {
        return response()->json([
            'success' => false,
            'message' => 'Trip image is empty.',
        ], 400);
    }
}

public function update_trip(Request $request)
{
    $trip_id = $request->input('trip_id');

    if (empty($trip_id)) {
        return response()->json([
            'success' => false,
            'message' => 'trip_id is empty.',
        ], 400);
    }

    // Retrieve the trip
    $trip = Trips::find($trip_id);

    if (!$trip) {
        return response()->json([
            'success' => false,
            'message' => 'Trip not found.',
        ], 404);
    }

    $user_id = $request->input('user_id'); 
    $trip_type = $request->input('trip_type');
    $from_date = $request->input('from_date');
    $to_date = $request->input('to_date');
    $trip_title = $request->input('trip_title');
    $trip_description = $request->input('trip_description');
    $location = $request->input('location');
    $trip_image = $request->file('trip_image');

    // Update trip details if provided
    if ($user_id !== null) {
        // Check if user_id is valid
        $user = Users::find($user_id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }
        $trip->user_id = $user_id;
    }
    if ($trip_type !== null) {
        if (empty($trip_type)) {
            return response()->json([
                'success' => false,
                'message' => 'Trip Type is empty.',
            ], 400);
        }
        $trip->trip_type = $trip_type;
    }
    if ($from_date !== null) {
        if (empty($from_date)) {
            return response()->json([
                'success' => false,
                'message' => 'From Date is empty.',
            ], 400);
        }
        $trip->from_date = $from_date;
    }
    if ($to_date !== null) {
        if (empty($to_date)) {
            return response()->json([
                'success' => false,
                'message' => 'To Date is empty.',
            ], 400);
        }
        $trip->to_date = $to_date;
    }
    if ($trip_title !== null) {
        if (empty($trip_title)) {
            return response()->json([
                'success' => false,
                'message' => 'Trip Title is empty.',
            ], 400);
        }
        $trip->trip_title = $trip_title;
    }
    if ($trip_description !== null) {
        if (empty($trip_description)) {
            return response()->json([
                'success' => false,
                'message' => 'Trip Description is empty.',
            ], 400);
        }
        $trip->trip_description = $trip_description;
    }
 
    if ($location !== null) {
        if (empty($location)) {
            return response()->json([
                'success' => false,
                'message' => 'Location is empty.',
            ], 400);
        }
        $trip->location = $location;
    }
    if ($trip_image !== null) {
        $imagePath = $trip_image->store('trips', 'public');
        $trip->trip_image = basename($imagePath);
    }

    $trip->trip_datetime = now(); 

    // Save the updated trip
    $trip->save();

        // Fetch user details associated with the trip
        $user = Users::find($trip->user_id);

            // Image URL
            $imageUrl = asset('storage/app/public/trips/' . $trip->trip_image);

    return response()->json([
        'success' => true,
        'message' => 'Trip updated successfully.',
        'data' => [
            'id' => $trip->id,
            'name' => $user->name,
            'unique_name' => $user->unique_name,
            'verified' => $user->verified,
            'trip_type' => $trip->trip_type,
            'from_date' => date('F j, Y', strtotime($trip->from_date)),
            'to_date' => date('F j, Y', strtotime($trip->to_date)),
            'time' => '4h',
            'trip_title' => $trip->trip_title,
            'trip_description' => $trip->trip_description,
            'location' => $trip->location,
            'trip_status' => $trip->trip_status,
            'trip_image' => $imageUrl,
            'trip_datetime' => Carbon::parse($trip->trip_datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($trip->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($trip->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 200);
}


public function trip_list(Request $request)
{
    // Check if user_id is provided
    if (!$request->has('user_id')) {
        return response()->json([
            'success' => false,
            'message' => 'User ID is required.',
        ], 400);
    }

    $userId = $request->input('user_id');

    // Validate user_id
    $userExists = Users::find($userId);
    if (!$userExists) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid User ID.',
        ], 400);
    }

    // Set default limit
    $limit = $request->has('limit') ? $request->input('limit') : 20;

    // Fetch trip details from the database with the specified limit in random order
    $trips = Trips::inRandomOrder()->limit($limit)->get();

    if ($trips->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No trips found.',
        ], 404);
    }

    $tripDetails = [];

    foreach ($trips as $trip) {
        $user = Users::find($trip->user_id);
        if ($user) {
            // Image URL
            $imageUrl = asset('storage/app/public/users/' . $user->profile);
            
            // Check if the user has friends
            $isFriend = Friends::where(function($query) use ($userId, $user) {
                $query->where('user_id', $userId)
                      ->where('friend_user_id', $user->id);
            })->orWhere(function($query) use ($userId, $user) {
                $query->where('user_id', $user->id)
                      ->where('friend_user_id', $userId);
            })->exists();
            
            $friendStatus = $isFriend ? '1' : '0';
        } else {
            $imageUrl = null; // Set default image URL if user not found
            $friendStatus = '0';
        }

        $tripimageUrl = asset('storage/app/public/trips/' . $trip->trip_image);

        $tripDetails[] = [
            'id' => $trip->id,
            'user_id' => $trip->user_id,
            'name' => $user->name,
            'unique_name' => $user->unique_name,
            'verified' => $user->verified,
            'profile' => $imageUrl,
            'trip_type' => $trip->trip_type,
            'from_date' => date('F j, Y', strtotime($trip->from_date)),
            'to_date' => date('F j, Y', strtotime($trip->to_date)),
            'time' => '4h',
            'friend' => $friendStatus,
            'trip_title' => $trip->trip_title,
            'trip_description' => $trip->trip_description,
            'location' => $trip->location,
            'trip_status' => $trip->trip_status,
            'trip_image' => $tripimageUrl,
            'trip_datetime' => Carbon::parse($trip->trip_datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($trip->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($trip->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Trip details retrieved successfully.',
        'data' => $tripDetails,
    ], 200);
}



public function my_trip_list(Request $request)
{
    // Get the user_id from the request
    $user_id = $request->input('user_id');

    // Fetch trips for the specific user_id from the database
    $trips = Trips::where('user_id', $user_id)->get();

    if ($trips->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No trips found for this user.',
        ], 404);
    }

    $tripDetails = [];

 
    foreach ($trips as $trip) {
        $user = Users::find($trip->user_id);
        if ($user) {
            // Image URL
            $imageUrl = asset('storage/app/public/users/' . $user->profile);
          
        } else {
            $imageUrl = null; // Set default image URL if user not found
        }

        $tripimageUrl = asset('storage/app/public/trips/' . $trip->trip_image);

        $tripDetails[] = [
            'id' => $trip->id,
            'user_id' => $trip->user_id,
            'name' => $user->name,
            'verified' => $user->verified,
            'unique_name' => $user->unique_name,
            'profile' => $imageUrl,
            'trip_type' => $trip->trip_type,
            'from_date' => date('F j, Y', strtotime($trip->from_date)),
            'to_date' => date('F j, Y', strtotime($trip->to_date)),
            'time' => '4h',
            'trip_title' => $trip->trip_title,
            'trip_description' => $trip->trip_description,
            'location' => $trip->location,
            'trip_status' => $trip->trip_status,
            'trip_image' => $tripimageUrl,
            'trip_datetime' => Carbon::parse($trip->trip_datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($trip->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($trip->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Trip details retrieved successfully for this user.',
        'data' => $tripDetails,
    ], 200);
}

public function trip_date(Request $request)
{
    // Get the date from the request
    $date = $request->input('date');

    // Validate the date input
    if (empty($date)) {
        return response()->json([
            'success' => false,
            'message' => 'Date is empty.',
        ], 400);
    }

    // Check if the date is in a valid format (YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid date format. Expected format: YYYY-MM-DD.',
        ], 400);
    }

    // Fetch trips for the specific date from the database, comparing only the date part of the datetime field
    $trips = Trips::whereDate('trip_datetime', $date)->get();

    if ($trips->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No trips found for this date.',
        ], 404);
    }

    $tripDetails = [];

   
    foreach ($trips as $trip) {
        $user = Users::find($trip->user_id);
        if ($user) {
            // Image URL
            $imageUrl = asset('storage/app/public/users/' . $user->profile);
            
        } else {
            $imageUrl = null; // Set default image URL if user not found
        }

        $tripimageUrl = asset('storage/app/public/trips/' . $trip->trip_image);

        $tripDetails[] = [
            'id' => $trip->id,
            'user_id' => $trip->user_id,
            'name' => $user->name,
            'verified' => $user->verified,
            'unique_name' => $user->unique_name,
            'profile' => $imageUrl,
            'trip_type' => $trip->trip_type,
            'from_date' => date('F j, Y', strtotime($trip->from_date)),
            'to_date' => date('F j, Y', strtotime($trip->to_date)),
            'time' => '4h',
            'trip_title' => $trip->trip_title,
            'trip_description' => $trip->trip_description,
            'location' => $trip->location,
            'trip_status' => $trip->trip_status,
            'trip_image' => $tripimageUrl,
            'trip_datetime' => Carbon::parse($trip->trip_datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($trip->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($trip->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Trip details retrieved successfully.',
        'data' => $tripDetails,
    ], 200);
}

public function latest_trip(Request $request)
{
    // Get the type from the request
    $type = $request->input('type');

    // Validate the type input
    if (empty($type)) {
        return response()->json([
            'success' => false,
            'message' => 'Type is empty.',
        ], 400);
    }

    if ($type == 'latest') {
        // Fetch the most recently added trip
        $trip = Trips::orderBy('created_at', 'desc')->first();
    } else {
        // Fetch the latest trip for the specified type
        $trip = Trips::where('type', $type)->orderBy('created_at', 'desc')->first();
    }

    if (!$trip) {
        return response()->json([
            'success' => false,
            'message' => 'No trips found.',
        ], 404);
    }

    $user = Users::find($trip->user_id);
    

    // Image URL
    $userProfileUrl = $user ? asset('storage/app/public/users/' . $user->profile) : null;
    $tripimageUrl = $trip ? asset('storage/app/public/trips/' . $trip->trip_image) : null;

    $tripDetails = [
        'id' => $trip->id,
        'user_id' => $trip->user_id,
        'name' => $user ? $user->name : 'Unknown',
        'verified' => $user->verified,
        'unique_name' => $user ? $user->unique_name : 'Unknown',
        'profile' => $userProfileUrl,
        'trip_type' => $trip->trip_type,
        'from_date' => date('F j, Y', strtotime($trip->from_date)),
        'to_date' => date('F j, Y', strtotime($trip->to_date)),
        'time' => '4h',
        'trip_title' => $trip->trip_title,
        'trip_description' => $trip->trip_description,
        'location' => $trip->location,
        'trip_status' => $trip->trip_status,
        'trip_image' => $tripimageUrl,
        'trip_datetime' => Carbon::parse($trip->trip_datetime)->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::parse($trip->updated_at)->format('Y-m-d H:i:s'),
        'created_at' => Carbon::parse($trip->created_at)->format('Y-m-d H:i:s'),
    ];

    return response()->json([
        'success' => true,
        'message' => 'Trip details retrieved successfully.',
        'data' => $tripDetails,
    ], 200);
}

public function recommend_trip_list(Request $request)
{
    // Get the trip_id from the request
    $trip_id = $request->input('trip_id');

    // Fetch trip details for the specific trip_id from the database
    $trip = Trips::find($trip_id);

    // Check if the trip exists
    if (!$trip) {
        return response()->json([
            'success' => false,
            'message' => 'Trip not found.',
        ], 404);
    }

    // Prepare trip details for the specific trip_id
    $tripDetails[] = [
        'id' => $trip->id,
        'user_id' => $trip->user_id,
        'trip_type' => $trip->trip_type,
        'from_date' => date('F j, Y', strtotime($trip->from_date)),
        'to_date' => date('F j, Y', strtotime($trip->to_date)),
        'time' => '4h',
        'trip_title' => $trip->trip_title,
        'trip_description' => $trip->trip_description,
        'location' => $trip->location,
        'trip_status' => $trip->trip_status,
        'trip_image' => asset('storage/app/public/trips/' . $trip->trip_image),
        'trip_datetime' => Carbon::parse($trip->trip_datetime)->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::parse($trip->updated_at)->format('Y-m-d H:i:s'),
        'created_at' => Carbon::parse($trip->created_at)->format('Y-m-d H:i:s'),
    ];

    // Fetch all trips from the database
    $allTrips = Trips::all();

    // Prepare trip details for all trips
    $allTripDetails = [];
    foreach ($allTrips as $trip) {
        $allTripDetails[] = [
            'id' => $trip->id,
            'user_id' => $trip->user_id,
            'trip_type' => $trip->trip_type,
            'from_date' => date('F j, Y', strtotime($trip->from_date)),
            'to_date' => date('F j, Y', strtotime($trip->to_date)),
            'time' => '4h',
            'trip_title' => $trip->trip_title,
            'trip_description' => $trip->trip_description,
            'location' => $trip->location,
            'trip_status' => $trip->trip_status,
            'trip_image' => asset('storage/app/public/trips/' . $trip->trip_image),
            'trip_datetime' => Carbon::parse($trip->trip_datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($trip->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($trip->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Trip details retrieved successfully.',
        'specific_trip' => $tripDetails,
        'all_trips' => $allTripDetails,
    ], 200);
}


public function delete_trip(Request $request)
{
    $trip_id = $request->input('trip_id');

    if (empty($trip_id)) {
        return response()->json([
            'success' => false,
            'message' => 'trip_id is empty.',
        ], 400);
    }

    // Fetch the offer from the database based on the provided offer_id
    $trip = Trips::find($trip_id);

    if (!$trip) {
        return response()->json([
            'success' => false,
            'message' => 'trip not found.',
        ], 404);
    }

    // Delete the offer
    $trip->delete();

    return response()->json([
        'success' => true,
        'message' => 'Trip deleted successfully.',
    ], 200);
}

public function add_chat(Request $request)
{
    $user_id = $request->input('user_id'); 
    $chat_user_id = $request->input('chat_user_id');
    $message = $request->input('message');

    // Validate user_id
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    // Validate chat_user_id
    if (empty($chat_user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'chat_user_id is empty.',
        ], 400);
    }

    // Validate message
    if (empty($message)) {
        return response()->json([
            'success' => false,
            'message' => 'Message is empty.',
        ], 400);
    }

    // Check if user_id and chat_user_id are the same
    if ($user_id == $chat_user_id) {
        return response()->json([
            'success' => false,
            'message' => 'You cannot chat with yourself.',
        ], 400);
    }

    // Check if user exists
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 404);
    }

    // Check if chat_user exists
    $chat_user = Users::find($chat_user_id);
    if (!$chat_user) {
        return response()->json([
            'success' => false,
            'message' => 'chat_user not found.',
        ], 404);
    }


     // Check if a chat between these users already exists
     $existingChat = Chats::where('user_id', $user_id)
     ->where('chat_user_id', $chat_user_id)
     ->first();
if ($existingChat) {
return response()->json([
'success' => false,
'message' => 'Chat between these users already exists.',
], 400);
}

    // Create a new chat instance
    $chat = new Chats();
    $chat->user_id = $user_id; 
    $chat->chat_user_id = $chat_user_id;
    $chat->latest_message = $message; 
    $chat->latest_msg_time = now();
    $chat->datetime = now(); 

    // Save the chat
    if (!$chat->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save Chat.',
        ], 500);
    }

    // Generate image URL for chat_user
    $chatUserImageUrl = asset('storage/app/public/users/' . $user->profile);

    // Return success response
    return response()->json([
        'success' => true,
        'message' => 'Chat added successfully.',
        'data' => [
            'id' => $chat->id,
            'user_id' => $chat->user_id,
            'chat_user_id' => $chat->chat_user_id,
            'name' => $chat_user->name, 
            'profile' => $chatUserImageUrl,
            'latest_message' => $chat->latest_message,
            'latest_msg_time' => Carbon::parse($chat->latest_msg_time)->format('Y-m-d H:i:s'),
            'msg_seen' => '0',
            'datetime' => Carbon::parse($chat->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($chat->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($chat->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 201);
}

public function chat_list(Request $request)
{
    // Fetching all chats from the Chats model
    $chats = Chats::all();

    if ($chats->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No chats found.',
        ], 404);
    }

    $chatDetails = $chats->map(function ($chat) {
        $chat_user = Users::find($chat->chat_user_id); // Fetch the chat_user details
        $imageUrl = asset('storage/app/public/users/' . $chat_user->profile);

          // Determine the format of last_seen
          $lastSeen = Carbon::parse($chat->latest_msg_time);
          $now = Carbon::now();
          $differenceDays = $now->diffInDays($lastSeen);
  
          if ($differenceDays == 0) {
              $lastSeenFormatted = $lastSeen->format('H:i'); // Today, show time
          } elseif ($differenceDays == 1) {
              $lastSeenFormatted = 'Yesterday'; // Yesterday
          } elseif ($differenceDays <= 7) {
              $lastSeenFormatted = $lastSeen->format('l'); // Last week, show day name
          } elseif ($differenceDays <= 14 && $lastSeen->isSameMonth($now)) {
              $lastSeenFormatted = 'Last week'; // Within 14 days and same month, show "Last week"
          } elseif ($lastSeen->month == $now->subMonths(1)->month) {
              $lastSeenFormatted = 'Last month'; // Last month
          } elseif ($lastSeen->isSameYear($now)) {
              $lastSeenFormatted = $lastSeen->format('M jS'); // This year, show month and day with ordinal indicator
          } else {
              $lastSeenFormatted = $lastSeen->format('M jS, Y'); // Older than current year, show month, day, and year
          }

        return [
            'id' => $chat->id,
            'user_id' => $chat->user_id,
            'chat_user_id' => $chat->chat_user_id,
            'name' => $chat_user->name, // Display chat_user name
            'profile' => $imageUrl, // Display chat_user profile
            'latest_message' => $chat->latest_message,
            'latest_msg_time' => $lastSeenFormatted,
            'msg_seen' => $chat->msg_seen,
            'datetime' => Carbon::parse($chat->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($chat->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($chat->created_at)->format('Y-m-d H:i:s'),
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Chat details listed successfully.',
        'data' => $chatDetails,
    ], 200);
}

public function add_friends(Request $request)
{
    $user_id = $request->input('user_id'); 
    $friend_user_id = $request->input('friend_user_id');
    $friend = $request->input('friend');

    // Validate user_id
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    // Validate friend_user_id
    if (empty($friend_user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'friend_user_id is empty.',
        ], 400);
    }

     // Check if user_id and friend_user_id are the same
     if ($user_id == $friend_user_id) {
        return response()->json([
            'success' => false,
            'message' => 'You cannot add yourself as a friend.',
        ], 400);
    }
    // Validate friend action
    if (!isset($friend)) {
        return response()->json([
            'success' => false,
            'message' => 'friend is empty.',
        ], 400);
    }

    // Check if user exists
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 404);
    }

    // Check if friend_user exists
    $friend_user = Users::find($friend_user_id);
    if (!$friend_user) {
        return response()->json([
            'success' => false,
            'message' => 'friend_user not found.',
        ], 404);
    }

    if ($friend == 2) {
        // Delete the friend relationship
        $existingFriend = Friends::where('user_id', $user_id)
                        ->where('friend_user_id', $friend_user_id)
                        ->first();

        if ($existingFriend) {
            $existingFriend->delete();

            return response()->json([
                'success' => true,
                'message' => 'Friend deleted successfully.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Friend relationship not found.',
            ], 404);
        }
    } else if ($friend == 1) {
        // Check if the friend relationship already exists
        $existingFriend = Friends::where('user_id', $user_id)
                                ->where('friend_user_id', $friend_user_id)
                                ->first();
        
        if ($existingFriend) {
            return response()->json([
                'success' => false,
                'message' => 'You have already added this friend.',
            ], 400);
        }
        $friend = new Friends();
        $friend->user_id = $user_id; 
        $friend->friend_user_id = $friend_user_id;
        $friend->status = 1;
        $friend->datetime = now(); 

        // Save the friend
        if (!$friend->save()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save friend.',
            ], 500);
        }

        // Generate image URLs
        $userImageUrl = asset('storage/app/public/users/' . $user->profile);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Friend added successfully.',
            'data' => [
                'id' => $friend->id,
                'user_id' => $friend->user_id,
                'name' => $user->name,
                'profile' => $userImageUrl,
                'friend_user_id' => $friend->friend_user_id,
                'friend_user_name' => $friend_user->name,
                'status' => $friend->status == 1 ? 'Interested' : 'Not Interested',
                'datetime' => Carbon::parse($friend->datetime)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($friend->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($friend->created_at)->format('Y-m-d H:i:s'),
            ],
        ], 201);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Invalid friend action.',
        ], 400);
    }
}


public function friends_list(Request $request)
{
    // Get the user_id from the request
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    // Fetching all friends from the Friends model where user_id matches
    $friends = Friends::where('user_id', $user_id)->get();

    if ($friends->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No friends found for the specified user.',
        ], 404);
    }

    $friendDetails = $friends->map(function ($friend) {
        $user = $friend->user;
        $friendUser = $friend->friendUser;


        $friendImageUrl = asset('storage/app/public/users/' . $friendUser->profile);

        // Determine the format of last_seen
        $lastSeen = Carbon::parse($user->last_seen);
        $now = Carbon::now();
        $differenceDays = $now->diffInDays($lastSeen);

        if ($differenceDays == 0) {
            $lastSeenFormatted = $lastSeen->format('H:i'); // Today, show time
        } elseif ($differenceDays == 1) {
            $lastSeenFormatted = 'Yesterday'; // Yesterday
        } elseif ($differenceDays <= 7) {
            $lastSeenFormatted = $lastSeen->format('l'); // Last week, show day name
        } elseif ($differenceDays <= 14 && $lastSeen->isSameMonth($now)) {
            $lastSeenFormatted = 'Last week'; // Within 14 days and same month, show "Last week"
        } elseif ($lastSeen->month == $now->subMonths(1)->month) {
            $lastSeenFormatted = 'Last month'; // Last month
        } elseif ($lastSeen->isSameYear($now)) {
            $lastSeenFormatted = $lastSeen->format('M jS'); // This year, show month and day with ordinal indicator
        } else {
            $lastSeenFormatted = $lastSeen->format('M jS, Y'); // Older than current year, show month, day, and year
        }

        return [
            'id' => $friend->id,
            'user_id' => $friend->user_id,
            'friend_user_id' => $friend->friend_user_id,
            'name' => $friendUser->name,
            'profile' => $friendImageUrl,
            'last_seen' => $lastSeenFormatted,
            'status' => $friend->status == 1 ? 'Interested' : 'Not Interested',
            'datetime' => Carbon::parse($friend->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($friend->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($friend->created_at)->format('Y-m-d H:i:s'),
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Friends details listed successfully for the specified user.',
        'data' => $friendDetails,
    ], 200);
}

public function add_notifications(Request $request)
{
    $user_id = $request->input('user_id'); 
    $notify_user_id = $request->input('notify_user_id');
    $message = $request->input('message');

    // Validate user_id
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    // Validate friend_user_id
    if (empty($notify_user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'notify_user_id is empty.',
        ], 400);
    }

      // Validate message
      if (empty($message)) {
        return response()->json([
            'success' => false,
            'message' => 'Message is empty.',
        ], 400);
    }

    // Check if user exists
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 404);
    }

    // Check if friend_user exists
    $notify_user = Users::find($notify_user_id);
    if (!$notify_user) {
        return response()->json([
            'success' => false,
            'message' => 'notify_user not found.',
        ], 404);
    }

    // Create a new friends instance
    $notification = new Notifications();
    $notification->user_id = $user_id; 
    $notification->notify_user_id = $notify_user_id;
    $notification->message = $message; 
    $notification->datetime = now(); 

    // Save the friend
    if (!$notification->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save Notifications.',
        ], 500);
    }

    // Generate image URLs
    $userImageUrl = asset('storage/app/public/users/' . $user->profile);

    // Return success response
    return response()->json([
        'success' => true,
        'message' => 'Notifications added successfully.',
        'data' => [
            'id' => $notification->id,
            'user_id' => $notification->user_id,
            'name' => $user->name,
            'profile' => $userImageUrl,
            'notify_user_id' => $notification->notify_user_id,
            'message' => $notification->message,
            'datetime' => Carbon::parse($notification->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($notification->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($notification->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 201);
}

public function notification_list(Request $request)
{
       // Get the user_id from the request
       $user_id = $request->input('user_id');

       if (empty($user_id)) {
           return response()->json([
               'success' => false,
               'message' => 'user_id is empty.',
           ], 400);
       }
   

    // Fetch notifications for the specific user_id from the database
    $notifications = Notifications::where('user_id', $user_id)->get();

    if ($notifications->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No notifications found for this user.',
        ], 404);
    }

    // Prepare notification details
    $notificationDetails = $notifications->map(function ($notification) {
        $user = Users::find($notification->user_id);
        $notify_user = Users::find($notification->notify_user_id);


        $notifyUserImageUrl = asset('storage/app/public/users/' . $notify_user->profile);
         // Calculate time difference in hours
         $notificationTime = Carbon::parse($notification->datetime);
         $currentTime = Carbon::now();
         $hoursDifference = $notificationTime->diffInHours($currentTime);
         
         // Determine the time display string
         if ($hoursDifference == 0) {
             $timeDifference = 'now';
         } else {
             $timeDifference = $hoursDifference . 'h';
         }
 
         return [
             'id' => $notification->id,
             'user_id' => $notification->user_id,
             'notify_user_id' => $notification->notify_user_id,
             'name' => $notify_user->name,
            'profile' => $notifyUserImageUrl,
             'message' => $notification->message,
             'datetime' => $notificationTime->format('Y-m-d H:i:s'),
             'time' => $timeDifference,  // Add this line to include the time difference in hours
             'updated_at' => Carbon::parse($notification->updated_at)->format('Y-m-d H:i:s'),
             'created_at' => Carbon::parse($notification->created_at)->format('Y-m-d H:i:s'),
         ];
     });
 
     return response()->json([
         'success' => true,
         'message' => 'Notification details retrieved successfully for this user.',
         'data' => $notificationDetails,
     ], 200);
 }

public function verifications(Request $request)
{
    $user_id = $request->input('user_id');
    $selfie_image = $request->file('selfie_image');
    $front_image = $request->file('front_image');
    $back_image = $request->file('back_image');

    // Validation for required fields
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }
    if (empty($selfie_image)) {
        return response()->json([
            'success' => false,
            'message' => 'Selfie Image is empty.',
        ], 400);
    }
    if (empty($front_image)) {
        return response()->json([
            'success' => false,
            'message' => 'Front Image is empty.',
        ], 400);
    }
    if (empty($back_image)) {
        return response()->json([
            'success' => false,
            'message' => 'Back Image is empty.',
        ], 400);
    }

    // Check if user exists
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 404);
    }

    // Store the images and get their paths
    $selfieImagePath = $selfie_image->store('verifications', 'public');
    $frontImagePath = $front_image->store('verifications', 'public');
    $backImagePath = $back_image->store('verifications', 'public');

    // Create a new verification record
    $verification = new Verifications();
    $verification->selfie_image = basename($selfieImagePath);
    $verification->front_image = basename($frontImagePath);
    $verification->back_image = basename($backImagePath);
    $verification->user_id = $user_id;
    $verification->save();

    // Image URLs
    $selfieImageUrl = asset('storage/app/public/verifications/' . $verification->selfie_image);
    $frontImageUrl = asset('storage/app/public/verifications/' . $verification->front_image);
    $backImageUrl = asset('storage/app/public/verifications/' . $verification->back_image);

    return response()->json([
        'success' => true,
        'message' => 'Verification added successfully.',
        'data' => [
            'id' => $verification->id,
            'user_name' => $user->name,
            'updated_at' => $verification->updated_at,
            'created_at' => $verification->created_at,
            'selfie_image_url' => $selfieImageUrl,
            'front_image_url' => $frontImageUrl,
            'back_image_url' => $backImageUrl,
        ],
    ], 201);
}
public function points_list(Request $request)
{
    // Fetch all points details from the database
    $points = Points::all();

    if ($points->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No Points found.',
        ], 404);
    }

    $pointsDetails = [];

    // Iterate through each points record and format the data
    foreach ($points as $point) {
        $pointsDetails[] = [
            'id' => $point->id,
            'points' => $point->points,
            'offer_percentage' => $point->offer_percentage,
            'price' => $point->price,
            'datetime' => Carbon::parse($point->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($point->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($point->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Points Details retrieved successfully.',
        'data' => $pointsDetails,
    ], 200);
}
}




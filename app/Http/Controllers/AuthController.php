<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users; 
use App\Models\Chats; 
use App\Models\Trips;
use App\Models\Interests; 
use App\Models\Notifications; 
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

    return response()->json([
        'success' => true,
        'registered' => true,
        'message' => 'Logged in successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'age' => $user->age,
            'gender' => $user->gender,
            'profession' => $user->profession,
            'refer_code' => $user->refer_code,
            'referred_by' => $user->referred_by,
            'profile' => $imageUrl,
            'points' => $user->points,
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 200);
}

public function register(Request $request)
{
    $mobile = $request->input('mobile');
    $age = $request->input('age');
    $name = $request->input('name');
    $email = $request->input('email');
    $gender = $request->input('gender');
    $profession = $request->input('profession');
    $referred_by = $request->input('referred_by');
    $points = $request->input('points', 10);

    // Validation for mandatory fields
    if (empty($mobile)) {
        return response()->json([
            'success' => false,
            'message' => 'Mobile is empty.',
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
    } elseif (!in_array(strtolower($gender), ['male', 'female'])) {
        return response()->json([
            'success' => false,
            'message' => 'Gender should be male or female.',
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

    // Remove non-numeric characters from the phone number
    $mobile = preg_replace('/[^0-9]/', '', $mobile);

    // Check if the length of the phone number is not equal to 10
    if (strlen($mobile) !== 10) {
        return response()->json([
            'success' => false,
            'message' => 'Mobile number should be exactly 10 digits.',
        ], 400);
    }

    // Check if the user with the given phone number already exists
    $existingUser = Users::where('mobile', $mobile)->first();
    if ($existingUser) {
        return response()->json([
            'success' => false,
            'message' => 'User already exists with this phone number.',
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

    // Create a new user instance
    $user = new Users();
    $user->mobile = $mobile;
    $user->age = $age;
    $user->name = $name;
    $user->gender = $gender;
    $user->profession = $profession;
    $user->refer_code = $refer_code; // Insert the generated refer_code
    $user->email = $email;
    $user->points = $points;
    $user->referred_by = $referred_by; // Insert referred_by if provided
    $user->datetime = now(); 
    $user->save();

    // Image URL
    $imageUrl = asset('storage/app/public/users/' . $user->profile);

    return response()->json([
        'success' => true,
        'message' => 'User registered successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'age' => $user->age,
            'gender' => $user->gender,
            'profession' => $user->profession,
            'refer_code' => $refer_code, // Return the generated refer_code
            'referred_by' => $user->referred_by,
            'profile' => $imageUrl,
            'points' => $user->points,
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 201);
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

return response()->json([
    'success' => true,
    'message' => 'User details retrieved successfully.',
    'data' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'mobile' => $user->mobile,
        'age' => $user->age,
        'gender' => $user->gender,
        'profession' => $user->profession,
        'refer_code' => $user->refer_code,
        'referred_by' => $user->referred_by,
        'profile' => $imageUrl,
        'points' => $user->points,
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

        return response()->json([
            'success' => true,
            'message' => 'User Profile updated successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'age' => $user->age,
                'gender' => $user->gender,
                'profession' => $user->profession,
                'refer_code' => $user->refer_code,
                'referred_by' => $user->referred_by,
                'profile' => $imageUrl,
                'points' => $user->points,
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
    $mobile = $request->input('mobile');
    $age = $request->input('age');
    $gender = $request->input('gender');
    $profession = $request->input('profession');

   // Validation for each field
   if ($mobile !== null) {
    // Remove non-numeric characters from the phone number
    $mobile = preg_replace('/[^0-9]/', '', $mobile);

    // Check if the length of the phone number is not equal to 10
    if (strlen($mobile) !== 10) {
        return response()->json([
            'success' => false,
            'message' => 'Mobile number should be exactly 10 digits.',
        ], 400);
    }
}

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

if ($gender !== null) {
    if (!in_array(strtolower($gender), ['male', 'female'])) {
        return response()->json([
            'success' => false,
            'message' => 'Gender should be male or female.',
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
    if ($mobile !== null) {
        $user->mobile = $mobile;
    }
    if ($age !== null) {
        $user->age = $age;
    }
    if ($gender !== null) {
        $user->gender = $gender;
    }
    if ($profession !== null) {
        $user->profession = $profession;
    }

    $user->datetime = now(); 

    $user->save();

    // Image URL
    $imageUrl = asset('storage/app/public/users/' . $user->profile);

    return response()->json([
        'success' => true,
        'message' => 'User Details updated successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'age' => $user->age,
            'gender' => $user->gender,
            'profession' => $user->profession,
            'refer_code' => $user->refer_code,
            'referred_by' => $user->referred_by,
            'profile' => $imageUrl,
            'points' => $user->points,
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 200);
}

public function add_trip(Request $request)
{
    $user_id = $request->input('user_id'); 
    $planning = $request->input('planning');
    $from_date = $request->input('from_date');
    $to_date = $request->input('to_date');
    $name_of_your_trip = $request->input('name_of_your_trip');
    $description_of_your_trip = $request->input('description_of_your_trip');
    $from_location = $request->input('from_location');
    $to_location = $request->input('to_location');
    $meetup_location = $request->input('meetup_location');

    $errors = [];

    if (empty($planning)) {
        $errors[] = 'planning is empty.';
    }
    if (empty($from_date)) {
        $errors[] = 'From Date is empty.';
    }
    if (empty($to_date)) {
        $errors[] = 'To Date is empty.';
    }
    if (empty($name_of_your_trip)) {
        $errors[] = 'Name Of Your Trip is empty.';
    }
    if (empty($description_of_your_trip)) {
        $errors[] = 'Description Of Your Trip is empty.';
    }
    if (empty($from_location)) {
        $errors[] = 'From Location is empty.';
    }
    if (empty($to_location)) {
        $errors[] = 'To Location is empty.';
    }
    if (empty($meetup_location)) {
        $errors[] = 'Meetup Location is empty.';
    }
 

      $user = Users::find($user_id);
      if (!$user) {
          $errors[] = 'user not found.';
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
    $trip->planning = $planning;
    $trip->from_date = $from_date;
    $trip->to_date = $to_date;
    $trip->name_of_your_trip = $name_of_your_trip;
    $trip->description_of_your_trip = $description_of_your_trip;
    $trip->from_location = $from_location;
    $trip->to_location = $to_location;
    $trip->meetup_location = $meetup_location;
    $trip->datetime = now(); 
    $trip->save();


    return response()->json([
        'success' => true,
        'message' => 'Trip Added successfully.',
        'data' => [
            'id' => $trip->id,
            'user_name' => $user->name,
            'planning' => $trip->planning,
            'from_date' => $trip->from_date,
            'to_date' => $trip->to_date,
            'name_of_your_trip' => $trip->name_of_your_trip,
            'description_of_your_trip' => $trip->description_of_your_trip,
            'from_location' => $trip->from_location,
            'to_location' => $trip->to_location,
            'meetup_location' => $trip->meetup_location,
            'datetime' => Carbon::parse($trip->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($trip->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($trip->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 201);
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
    $planning = $request->input('planning');
    $from_date = $request->input('from_date');
    $to_date = $request->input('to_date');
    $name_of_your_trip = $request->input('name_of_your_trip');
    $description_of_your_trip = $request->input('description_of_your_trip');
    $from_location = $request->input('from_location');
    $to_location = $request->input('to_location');
    $meetup_location = $request->input('meetup_location');

    // Update trip details
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
    if ($planning !== null) {
        $trip->planning = $planning;
    }
    if ($from_date !== null) {
        $trip->from_date = $from_date;
    }
    if ($to_date !== null) {
        $trip->to_date = $to_date;
    }
    if ($name_of_your_trip !== null) {
        $trip->name_of_your_trip = $name_of_your_trip;
    }
    if ($description_of_your_trip !== null) {
        $trip->description_of_your_trip = $description_of_your_trip;
    }
    if ($from_location !== null) {
        $trip->from_location = $from_location;
    }
    if ($to_location !== null) {
        $trip->to_location = $to_location;
    }
    if ($meetup_location !== null) {
        $trip->meetup_location = $meetup_location;
    }
    $trip->datetime = now(); 

    // Save the updated trip
    $trip->save();

    return response()->json([
        'success' => true,
        'message' => 'Trip updated successfully.',
        'data' => [
            'id' => $trip->id,
            'user_name' => $user->name,
            'planning' => $trip->planning,
            'from_date' => $trip->from_date,
            'to_date' => $trip->to_date,
            'name_of_your_trip' => $trip->name_of_your_trip,
            'description_of_your_trip' => $trip->description_of_your_trip,
            'from_location' => $trip->from_location,
            'to_location' => $trip->to_location,
            'meetup_location' => $trip->meetup_location,
            'datetime' => Carbon::parse($trip->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($trip->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($trip->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 200);
}

public function trip_list(Request $request)
{
    // Set default limit
    $limit = $request->has('limit') ? $request->input('limit') : 1;

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
        $tripDetails[] = [
            'id' => $trip->id,
            'user_name' => $user->name,
            'planning' => $trip->planning,
            'From Date' => $trip->from_date,
            'To Date' => $trip->to_date,
            'Name Of Your Trip' => $trip->name_of_your_trip,
            'Description Of Your Trip' => $trip->description_of_your_trip,
            'from_location' => $trip->from_location,
            'to_location' => $trip->to_location,
            'meetup_location' => $trip->meetup_location,
            'datetime' => Carbon::parse($trip->datetime)->format('Y-m-d H:i:s'),
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
        $tripDetails[] = [
            'id' => $trip->id,
            'user_id' => $trip->user_id,
            'user_name' => $user->name,
            'planning' => $trip->planning,
            'From Date' => $trip->from_date,
            'To Date' => $trip->to_date,
            'Name Of Your Trip' => $trip->name_of_your_trip,
            'Description Of Your Trip' => $trip->description_of_your_trip,
            'from_location' => $trip->from_location,
            'to_location' => $trip->to_location,
            'meetup_location' => $trip->meetup_location,
            'datetime' => Carbon::parse($trip->datetime)->format('Y-m-d H:i:s'),
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
    $trips = Trips::whereDate('datetime', $date)->get();

    if ($trips->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No trips found for this date.',
        ], 404);
    }

    $tripDetails = [];

    foreach ($trips as $trip) {
        $user = Users::find($trip->user_id);
        $tripDetails[] = [
            'id' => $trip->id,
            'user_id' => $trip->user_id,
            'user_name' => $user->name,
            'planning' => $trip->planning,
            'From Date' => $trip->from_date,
            'To Date' => $trip->to_date,
            'Name Of Your Trip' => $trip->name_of_your_trip,
            'Description Of Your Trip' => $trip->description_of_your_trip,
            'from_location' => $trip->from_location,
            'to_location' => $trip->to_location,
            'meetup_location' => $trip->meetup_location,
            'datetime' => Carbon::parse($trip->datetime)->format('Y-m-d H:i:s'),
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

    $tripDetails = [
        'id' => $trip->id,
        'user_id' => $trip->user_id,
        'user_name' => $user ? $user->name : 'Unknown',
        'planning' => $trip->planning,
        'From Date' => $trip->from_date,
        'To Date' => $trip->to_date,
        'Name Of Your Trip' => $trip->name_of_your_trip,
        'Description Of Your Trip' => $trip->description_of_your_trip,
        'from_location' => $trip->from_location,
        'to_location' => $trip->to_location,
        'meetup_location' => $trip->meetup_location,
        'datetime' => Carbon::parse($trip->datetime)->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::parse($trip->updated_at)->format('Y-m-d H:i:s'),
        'created_at' => Carbon::parse($trip->created_at)->format('Y-m-d H:i:s'),
    ];

    return response()->json([
        'success' => true,
        'message' => 'Trip details retrieved successfully.',
        'data' => $tripDetails,
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
        $user = $chat->user;
        $imageUrl = asset('storage/app/public/users/' . $user->profile);

        return [
            'id' => $chat->id,
            'user_id' => $chat->user_id,
            'user_name' => $user->name, 
            'user_profile' => $imageUrl,
            'latest_message' => $chat->latest_message,
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

public function add_interests(Request $request)
{
    $user_id = $request->input('user_id'); 
    $interest_user_id = $request->input('interest_user_id');

    // Validate user_id
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    // Validate interest_user_id
    if (empty($interest_user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'interest_user_id is empty.',
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

    // Check if interest_user exists
    $interest_user = Users::find($interest_user_id);
    if (!$interest_user) {
        return response()->json([
            'success' => false,
            'message' => 'interest_user not found.',
        ], 404);
    }

    // Create a new Interests instance
    $interest = new Interests();
    $interest->user_id = $user_id; 
    $interest->interest_user_id = $interest_user_id;
    $interest->status = 1;
    $interest->datetime = now(); 

    // Save the interest
    if (!$interest->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save interest.',
        ], 500);
    }

    // Generate image URLs
    $userImageUrl = asset('storage/app/public/users/' . $user->profile);

    // Return success response
    return response()->json([
        'success' => true,
        'message' => 'Interests added successfully.',
        'data' => [
            'id' => $interest->id,
            'user_id' => $interest->user_id,
            'user_name' => $user->name,
            'user_profile' => $userImageUrl,
            'interest_user_id' => $interest->interest_user_id,
            'interest_user_name' => $interest_user->name,
            'status' => $interest->status == 1 ? 'Interested' : 'Not Interested',
            'datetime' => Carbon::parse($interest->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($interest->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($interest->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 201);
}

public function interest_list(Request $request)
{
    // Fetching all chats from the Chats model
    $interests = Interests::all();

    if ($interests->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No interests found.',
        ], 404);
    }

    $interestDetails = $interests->map(function ($interest) {
        $user = $interest->user;
        $imageUrl = asset('storage/app/public/users/' . $user->profile);

        return [
            'id' => $interest->id,
            'user_id' => $interest->user_id,
            'user_name' => $user->name,
            'user_profile' => $imageUrl,
            'interest_user_id' => $interest->interest_user_id,
            'status' => $interest->status == 1 ? 'Interested' : 'Not Interested',
            'datetime' => Carbon::parse($interest->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($interest->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($interest->created_at)->format('Y-m-d H:i:s'),
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Chat details listed successfully.',
        'data' => $interestDetails,
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

    // Validate interest_user_id
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

    // Check if interest_user exists
    $notify_user = Users::find($notify_user_id);
    if (!$notify_user) {
        return response()->json([
            'success' => false,
            'message' => 'notify_user not found.',
        ], 404);
    }

    // Create a new Interests instance
    $notification = new Notifications();
    $notification->user_id = $user_id; 
    $notification->notify_user_id = $notify_user_id;
    $notification->message = $message; 
    $notification->datetime = now(); 

    // Save the interest
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
            'user_name' => $user->name,
            'user_profile' => $userImageUrl,
            'notify_user_id' => $notification->notify_user_id,
            'notify_user_name' => $notify_user->name,
            'message' => $notification->message,
            'datetime' => Carbon::parse($notification->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($notification->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($notification->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 201);
}

public function notification_list(Request $request)
{
    // Validate the request input
    $validatedData = $request->validate([
        'user_id' => 'required|exists:users,id',
    ]);

    // Get the user_id from the validated data
    $user_id = $validatedData['user_id'];

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

        // Generate image URLs
        $userImageUrl = asset('storage/users/' . $user->profile);

        return [
            'id' => $notification->id,
            'user_id' => $notification->user_id,
            'user_name' => $user->name,
            'user_profile' => $userImageUrl,
            'notify_user_id' => $notification->notify_user_id,
            'notify_user_name' => $notify_user->name,
            'message' => $notification->message,
            'datetime' => Carbon::parse($notification->datetime)->format('Y-m-d H:i:s'),
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


}




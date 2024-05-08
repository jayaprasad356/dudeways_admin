<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users; 
use App\Models\Trips; 

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
            $response['success'] = false;
            $response['message'] = 'mobile not registered.';
            return response()->json($response, 404);
        }

    // Image URL
    $imageUrl = asset('storage/app/public/users/' . $user->profile);

    return response()->json([
        'success' => true,
        'message' => 'Logged in successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'age' => $user->age,
            'gender' => $user->gender,
            'profession' => $user->profession,
            'refer_code' => $user->refer_code,
            'referred_by' => $user->referred_by,
            'profile' => $imageUrl,
            'updated_at' => $user->updated_at,
            'created_at' => $user->created_at,
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

    $errors = [];

    // Validation for mandatory fields
    if (empty($mobile)) {
        $errors[] = 'Mobile is empty.';
    }
    if (empty($age)) {
        $errors[] = 'Age is empty.';
    }
    if (empty($name)) {
        $errors[] = 'Name is empty.';
    }
    if (empty($gender)) {
        $errors[] = 'Gender is empty.';
    }
    if (empty($profession)) {
        $errors[] = 'Profession is empty.';
    }
    if (empty($email)) {
        $errors[] = 'Email is empty.';
    }
   // Remove non-numeric characters from the phone number
   $mobile = preg_replace('/[^0-9]/', '', $mobile);
    
   // Check if the length of the phone number is not equal to 10
   if (strlen($mobile) !== 10) {
       $response['success'] = false;
       $response['message'] = "mobile number should be exactly 10 digits";
       return response()->json($response, 400);
   }
    // Check if any validation errors occurred
    if (!empty($errors)) {
        return response()->json([
            'success' => false,
            'message' => $errors,
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
    $user->referred_by = $referred_by; // Insert referred_by if provided
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
            'phone' => $user->mobile,
            'age' => $user->age,
            'gender' => $user->gender,
            'profession' => $user->profession,
            'refer_code' => $refer_code, // Return the generated refer_code
            'referred_by' => $user->referred_by,
            'profile' => $imageUrl,
            'updated_at' => $user->updated_at,
            'created_at' => $user->created_at,
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
        'phone' => $user->mobile,
        'age' => $user->age,
        'gender' => $user->gender,
        'profession' => $user->profession,
        'refer_code' => $user->refer_code,
        'referred_by' => $user->referred_by,
        'profile' => $imageUrl,
        'updated_at' => $user->updated_at,
        'created_at' => $user->created_at,
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


    // Update offer details

    if ($profile !== null) {
        $imagePath = $profile->store('users', 'public');
        $user->profile = basename($imagePath);
    }
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
            'phone' => $user->mobile,
            'age' => $user->age,
            'gender' => $user->gender,
            'profession' => $user->profession,
            'refer_code' => $user->refer_code,
            'referred_by' => $user->referred_by,
            'profile' => $imageUrl,
            'updated_at' => $user->updated_at,
            'created_at' => $user->created_at,
        ],
    ], 200);
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

      // Remove non-numeric characters from the phone number
      $mobile = preg_replace('/[^0-9]/', '', $mobile);
    
      // Check if the length of the phone number is not equal to 10
      if (strlen($mobile) !== 10) {
          $response['success'] = false;
          $response['message'] = "mobile number should be exactly 10 digits";
          return response()->json($response, 400);
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
            'phone' => $user->mobile,
            'age' => $user->age,
            'gender' => $user->gender,
            'profession' => $user->profession,
            'refer_code' => $user->refer_code,
            'referred_by' => $user->referred_by,
            'profile' => $imageUrl,
            'updated_at' => $user->updated_at,
            'created_at' => $user->created_at,
        ],
    ], 200);
}
public function Trip_list(Request $request)
{
    // Fetch all trip details from the database
    $trips = Trips::all();

    if ($trips->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No trips found.',
        ], 404);
    }

    $tripDetails = [];

    foreach ($trips as $trip) {
        $tripDetails[] = [
            'id' => $trip->id,
            'planning' => $trip->planning,
            'location' => $trip->location,
            'From Date' => $trip->from_date,
            'To Date' => $trip->to_date,
            'Name Of Your Trip' => $trip->name_of_your_trip,
            'Description Of Your Trip' => $trip->description_of_your_trip,
            'updated_at' => $trip->updated_at,
            'created_at' => $trip->created_at,
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Trip details retrieved successfully.',
        'data' => $tripDetails,
    ], 200);
}
}


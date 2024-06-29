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
use App\Models\Transaction; 
use App\Models\Feedback; 
use App\Models\Professions; 
use App\Models\News; 
use Carbon\Carbon;
use Berkayk\OneSignal\OneSignalClient;

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
        'introduction' => $user->introduction,
        'message_notify' => $user->message_notify,
        'add_friend_notify' => $user->add_friend_notify,
        'view_notify' => $user->view_notify,
        'profile_verified' => $user->profile_verified,
        'cover_img_verified' => $user->cover_img_verified,
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
    $introduction = $request->input('introduction');
    $points = $request->input('points', 50);
    $total_points = $request->input('total_points', 50);

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
    if (empty($introduction)) {
        return response()->json([
            'success' => false,
            'message' => 'introduction is empty.',
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
    $user->total_points = $total_points;
    $user->state = $state;
    $user->city = $city;
    $user->referred_by = $referred_by;
    $user->introduction = $introduction;
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
            'total_points' => $user->total_points,
            'introduction' => $user->introduction,
            'latitude' => $user->latitude,
            'longtitude' => $user->longtitude,
            'verified' => 0,
            'online_status' => 0,
            'message_notify' => 1,
            'add_friend_notify' => 1,
            'view_notify' => 1,
            'profile_verified' => 0,
            'cover_img_verified' => 0,
            'last_seen' => Carbon::parse($user->last_seen)->format('Y-m-d H:i:s'),
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 201);
}

private function generateUniqueName($name, $user_id)
{
    // Remove spaces and convert the name to lowercase
    $name = strtolower(str_replace(' ', '', $name));

    // Extract the first part of the user's name and limit to first 8 characters
    $firstPart = substr($name, 0, 8);

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

    // Fetch the user details from the database based on the provided user_id
    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 404);
    }

    // Image URLs
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
            'introduction' => $user->introduction,
            'message_notify' => $user->message_notify,
            'add_friend_notify' => $user->add_friend_notify,
            'view_notify' => $user->view_notify,
            'profile_verified' => $user->profile_verified,
            'cover_img_verified' => $user->cover_img_verified,
            'last_seen' => Carbon::parse($user->last_seen)->format('Y-m-d H:i:s'),
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 200);
}

public function other_userdetails(Request $request)
{
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    // Fetch the user details from the database based on the provided user_id
    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 404);
    }

    // Image URLs

    $imageUrl = $user->profile_verified == 1 ? asset('storage/app/public/users/' . $user->profile) : '';
    $coverImageUrl = $user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $user->cover_img) : '';
    

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
            'cover_img' => $coverImageUrl,
            'points' => $user->points,
            'verified' => $user->verified,
            'online_status' => $user->online_status,
            'introduction' => $user->introduction,
            'message_notify' => $user->message_notify,
            'add_friend_notify' => $user->add_friend_notify,
            'view_notify' => $user->view_notify,
            'profile_verified' => $user->profile_verified,
            'cover_img_verified' => $user->cover_img_verified,
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
                'introduction' => $user->introduction,
                'message_notify' => $user->message_notify,
                'add_friend_notify' => $user->add_friend_notify,
                'view_notify' => $user->view_notify,
                'profile_verified' => $user->profile_verified,
                'cover_img_verified' => $user->cover_img_verified,
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
            'message' => 'Cover Image updated successfully.',
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
                'introduction' => $user->introduction,
                'message_notify' => $user->message_notify,
                'add_friend_notify' => $user->add_friend_notify,
                'view_notify' => $user->view_notify,
                'profile_verified' => $user->profile_verified,
                'cover_img_verified' => $user->cover_img_verified,
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
    $introduction = $request->input('introduction');

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
    if ($introduction !== null) {
        $user->introduction = $introduction;
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
            'introduction' => $user->introduction,
            'message_notify' => $user->message_notify,
            'add_friend_notify' => $user->add_friend_notify,
            'view_notify' => $user->view_notify,
            'profile_verified' => $user->profile_verified,
            'cover_img_verified' => $user->cover_img_verified,
            'last_seen' => Carbon::parse($user->last_seen)->format('Y-m-d H:i:s'),
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 200);
}

public function update_location(Request $request)
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

    $latitude = $request->input('latitude');
    $longtitude = $request->input('longtitude');

    if (is_null($latitude)) {
        return response()->json([
            'success' => false,
            'message' => 'latitude is empty.',
        ], 400);
    }

    if (is_null($longtitude)) {
        return response()->json([
            'success' => false,
            'message' => 'longtitude is empty.',
        ], 400);
    }

    // Update user location details
    $user->latitude = $latitude;
    $user->longtitude = $longtitude;

    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'User location updated successfully.',
    ], 200);
}

public function update_notify(Request $request)
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

    $message_notify = $request->input('message_notify');
    $add_friend_notify = $request->input('add_friend_notify');
    $view_notify = $request->input('view_notify');

    if (is_null($message_notify)) {
        return response()->json([
            'success' => false,
            'message' => 'Message Notify is empty.',
        ], 400);
    }

    if (is_null($add_friend_notify)) {
        return response()->json([
            'success' => false,
            'message' => 'Add Friend Notify is empty.',
        ], 400);
    }

    if (is_null($view_notify)) {
        return response()->json([
            'success' => false,
            'message' => 'View Notify is empty.',
        ], 400);
    }


    // Update user location details
    $user->message_notify = $message_notify;
    $user->add_friend_notify = $add_friend_notify;
    $user->view_notify = $view_notify;

    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'User notify updated successfully.',
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


   // Create a new trip instance
   $trip = new trips();
   $trip->user_id = $user_id;
   $trip->trip_type = $trip_type;
   $trip->from_date = Carbon::parse($from_date)->format('Y-m-d');
   $trip->to_date = Carbon::parse($to_date)->format('Y-m-d');
   $trip->trip_title = $trip_title;
   $trip->trip_description = $trip_description;
   $trip->location = $location;
   $trip->trip_datetime = now();
   $trip->save();

        // Image URL
        $imageUrl = asset('storage/app/public/trips/' . $trip->trip_image);
  // Fetch user details associated with the trip
  $user = Users::find($trip->user_id);

   // Calculate time difference in hours
 $tripTime = Carbon::parse($trip->trip_datetime);
 $currentTime = Carbon::now();
 $hoursDifference = $tripTime->diffInHours($currentTime);
 
 // Determine the time display string
 if ($hoursDifference == 0) {
     $timeDifference = 'now';
 } elseif ($hoursDifference < 24) {
     $timeDifference = $hoursDifference . 'h';
 } else {
     $daysDifference = floor($hoursDifference / 24);
     $timeDifference = $daysDifference . 'd';
 }


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
            'time' => $timeDifference, 
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
        $trip->from_date = Carbon::parse($from_date)->format('Y-m-d');
    }
    if ($to_date !== null) {
        if (empty($to_date)) {
            return response()->json([
                'success' => false,
                'message' => 'To Date is empty.',
            ], 400);
        }
        $trip->to_date = Carbon::parse($to_date)->format('Y-m-d');
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

            // Calculate time difference in hours
 $tripTime = Carbon::parse($trip->trip_datetime);
 $currentTime = Carbon::now();
 $hoursDifference = $tripTime->diffInHours($currentTime);
 
 // Determine the time display string
 if ($hoursDifference == 0) {
     $timeDifference = 'now';
 } elseif ($hoursDifference < 24) {
     $timeDifference = $hoursDifference . 'h';
 } else {
     $daysDifference = floor($hoursDifference / 24);
     $timeDifference = $daysDifference . 'd';
 }


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
            'time' => $timeDifference, 
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
    // Validate user_id
    if (!$request->has('user_id')) {
        return response()->json([
            'success' => false,
            'message' => 'User ID is required.',
        ], 400);
    }

    $userId = $request->input('user_id');

    $userExists = Users::find($userId);
    if (!$userExists) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid User ID.',
        ], 400);
    }

    // Validate type
    if (!$request->has('type')) {
        return response()->json([
            'success' => false,
            'message' => 'Type is empty.',
        ], 400);
    }

    $type = $request->input('type');

  

    // Fetch the user's latitude and longitude
    $userLatitude = (float)$userExists->latitude;
    $userLongtitude = (float)$userExists->longtitude;

    // Initialize trip details array
    $tripDetails = [];

    $currentDate = Carbon::now()->toDateString();

    if ($type == 'latest') {
        // Fetch the latest trips with trip_status 1
        $trips = Trips::where('trip_status', 1)
            ->whereDate('from_date', '>=', $currentDate)
            ->orderBy('trip_datetime', 'desc');

        foreach ($trips as $trip) {
            $tripUser = Users::find($trip->user_id);

            // Check if user exists
            if (!$tripUser) {
                continue; // Skip this trip if user does not exist
            }

            // Calculate the distance for each trip
            $distance = $this->calculateDistance($userLatitude, $userLongtitude, (float)$tripUser->latitude, (float)$tripUser->longtitude);

            $tripDetails[] = [
                'trip' => $trip,
                'user' => $tripUser,
                'distance' => $distance
            ];
        }
    } elseif ($type == 'nearby') {
        // Fetch trips with trip_status 1 and paginate the results
        $trips = Trips::where('trip_status', 1)
            ->whereDate('from_date', '>=', $currentDate);
    
        // Array to hold trips with calculated distances
        $tripsWithDistance = [];
    
        foreach ($trips as $trip) {
            $tripUser = Users::find($trip->user_id);
    
            // Check if user exists
            if (!$tripUser) {
                continue; // Skip this trip if user does not exist
            }
    
            // Calculate the distance between the users using their latitude and longitude
            $distance = $this->calculateDistance($userLatitude, $userLongtitude, (float)$tripUser->latitude, (float)$tripUser->longtitude);
    
            // Store trip details along with distance
            $tripsWithDistance[] = [
                'trip' => $trip,
                'user' => $tripUser,
                'distance' => $distance
            ];
        }
    
        // Sort trips by distance (ascending order)
        usort($tripsWithDistance, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
    
        // Prepare formatted trip details
        $tripDetailsFormatted = [];
    
        foreach ($tripsWithDistance as $detail) {
            $trip = $detail['trip'];
            $user = $detail['user'];
            $distance = $detail['distance'];
    
            $imageUrl = $user->profile_verified == 1 ? asset('storage/app/public/users/' . $user->profile) : '';
            $coverImageUrl = $user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $user->cover_img) : '';
    
            // Check if the user is a friend
            $isFriend = Friends::where('user_id', $userId)
                ->where('friend_user_id', $user->id)
                ->exists();
    
            $friendStatus = $isFriend ? '1' : '0';
    
            // Calculate time difference in hours
            $tripTime = Carbon::parse($trip->trip_datetime);
            $currentTime = Carbon::now();
            $hoursDifference = $tripTime->diffInHours($currentTime);
    
            // Determine the time display string
            if ($hoursDifference == 0) {
                $timeDifference = 'now';
            } elseif ($hoursDifference < 24) {
                $timeDifference = $hoursDifference . 'h';
            } else {
                $daysDifference = floor($hoursDifference / 24);
                $timeDifference = $daysDifference . 'd';
            }
    
            $tripImageUrl = asset('storage/app/public/trips/' . $trip->trip_image);
    
            $tripDetailsFormatted[] = [
                'id' => $trip->id,
                'user_id' => $trip->user_id,
                'name' => $user->name,
                'unique_name' => $user->unique_name,
                'verified' => $user->verified,
                'profile' => $imageUrl,
                'cover_image' => $coverImageUrl,
                'trip_type' => $trip->trip_type,
                'from_date' => date('F j, Y', strtotime($trip->from_date)),
                'to_date' => date('F j, Y', strtotime($trip->to_date)),
                'time' => $timeDifference,
                'friend' => $friendStatus,
                'trip_title' => $trip->trip_title,
                'trip_description' => $trip->trip_description,
                'location' => $trip->location,
                'trip_status' => $trip->trip_status,
                'trip_image' => $tripImageUrl,
                'trip_datetime' => Carbon::parse($trip->trip_datetime)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($trip->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($trip->created_at)->format('Y-m-d H:i:s'),
                'distance' => number_format($distance, 2) . ' km'
            ];
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Trip details retrieved successfully.',
            'data' => $tripDetailsFormatted,
            'pagination' => [
                'current_page' => $trips->currentPage(),
                'per_page' => $trips->perPage(),
                'total' => $trips->total(),
                'last_page' => $trips->lastPage(),
            ]
        ], 200);
    
    } elseif ($type == 'date') {
        // Check if the date parameter is provided
        if (!$request->has('date')) {
            return response()->json([
                'success' => false,
                'message' => 'Date is empty.',
            ], 400);
        }

        $fromDate = $request->input('date');

        // Check if the date is in a valid format (YYYY-MM-DD)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format. Expected format: YYYY-MM-DD.',
            ], 400);
        }

        // Fetch trips with the specified from_date and trip_status 1
        $trips = Trips::where('trip_status', 1)
            ->whereDate('from_date', $fromDate)
            ->orderBy('created_at', 'desc');

        foreach ($trips as $trip) {
            $tripUser = Users::find($trip->user_id);

            // Check if user exists
            if (!$tripUser) {
                continue; // Skip this trip if user does not exist
            }

            // Calculate the distance for each trip
            $distance = $this->calculateDistance($userLatitude, $userLongtitude, (float)$tripUser->latitude, (float)$tripUser->longtitude);

            $tripDetails[] = [
                'trip' => $trip,
                'user' => $tripUser,
                'distance' => $distance
            ];
        }
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Invalid type provided',
        ], 400);
    }

    if (empty($tripDetails)) {
        return response()->json([
            'success' => false,
            'message' => 'No trips found.',
        ], 404);
    }

    // Format trip details for response
    $tripDetailsFormatted = [];

    foreach ($tripDetails as $detail) {
        $trip = $detail['trip'];
        $user = $detail['user'];
        $distance = $detail['distance'];

        $imageUrl = $user->profile_verified == 1 ? asset('storage/app/public/users/' . $user->profile) : '';
        $coverImageUrl = $user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $user->cover_img) : '';

        // Check if the user is a friend
        $isFriend = Friends::where('user_id', $userId)
            ->where('friend_user_id', $user->id)
            ->exists();

        $friendStatus = $isFriend ? '1' : '0';

        // Calculate time difference in hours
        $tripTime = Carbon::parse($trip->trip_datetime);
        $currentTime = Carbon::now();
        $hoursDifference = $tripTime->diffInHours($currentTime);

        // Determine the time display string
        if ($hoursDifference == 0) {
            $timeDifference = 'now';
        } elseif ($hoursDifference < 24) {
            $timeDifference = $hoursDifference . 'h';
        } else {
            $daysDifference = floor($hoursDifference / 24);
            $timeDifference = $daysDifference . 'd';
        }

        $tripImageUrl = asset('storage/app/public/trips/' . $trip->trip_image);

        $tripDetailsFormatted[] = [
            'id' => $trip->id,
            'user_id' => $trip->user_id,
            'name' => $user->name,
            'unique_name' => $user->unique_name,
            'verified' => $user->verified,
            'profile' => $imageUrl,
            'cover_image' => $coverImageUrl,
            'trip_type' => $trip->trip_type,
            'from_date' => date('F j, Y', strtotime($trip->from_date)),
            'to_date' => date('F j, Y', strtotime($trip->to_date)),
            'time' => $timeDifference,
            'friend' => $friendStatus,
            'trip_title' => $trip->trip_title,
            'trip_description' => $trip->trip_description,
            'location' => $trip->location,
            'trip_status' => $trip->trip_status,
            'trip_image' => $tripImageUrl,
            'trip_datetime' => Carbon::parse($trip->trip_datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($trip->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($trip->created_at)->format('Y-m-d H:i:s'),
            'distance' => number_format($distance, 2) . ' km'
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Trip details retrieved successfully.',
        'data' => $tripDetailsFormatted,
        'pagination' => [
            'current_page' => $trips->currentPage(),
            'per_page' => $trips->perPage(),
            'total' => $trips->total(),
            'last_page' => $trips->lastPage(),
        ]
    ], 200);
}



private function calculateDistance($latitudeFrom, $longtitudeFrom, $latitudeTo, $longtitudeTo, $earthRadius = 6371)
{
    // Convert latitude and longitude from degrees to radians
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longtitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longtitudeTo);

    // Calculate differences in latitude and longitude
    $latDiff = $latTo - $latFrom;
    $lonDiff = $lonTo - $lonFrom;

    // Calculate using Haversine formula
    $a = sin($latDiff / 2) * sin($latDiff / 2) + cos($latFrom) * cos($latTo) * sin($lonDiff / 2) * sin($lonDiff / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Distance in kilometers
    $distance = $earthRadius * $c;

    return $distance;
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
            'message' => 'No trips found.',
        ], 404);
    }

    $tripDetails = [];

 
    foreach ($trips as $trip) {
        $user = Users::find($trip->user_id);
        if ($user) {
           
            $imageUrl = asset('storage/app/public/users/' . $user->profile) ;
            $coverimageUrl = asset('storage/app/public/users/' . $user->cover_img) ;
            
          
        } else {
            $imageUrl = null; // Set default image URL if user not found
            $coverimageUrl = null; // Set default image URL if user not found
        }

      // Calculate time difference in hours
 $tripTime = Carbon::parse($trip->trip_datetime);
 $currentTime = Carbon::now();
 $hoursDifference = $tripTime->diffInHours($currentTime);
 
 // Determine the time display string
 if ($hoursDifference == 0) {
     $timeDifference = 'now';
 } elseif ($hoursDifference < 24) {
     $timeDifference = $hoursDifference . 'h';
 } else {
     $daysDifference = floor($hoursDifference / 24);
     $timeDifference = $daysDifference . 'd';
 }


        $tripimageUrl = asset('storage/app/public/trips/' . $trip->trip_image);

        $tripDetails[] = [
            'id' => $trip->id,
            'user_id' => $trip->user_id,
            'name' => $user->name,
            'verified' => $user->verified,
            'unique_name' => $user->unique_name,
            'profile' => $imageUrl,
            'cover_image' => $coverimageUrl,
            'trip_type' => $trip->trip_type,
            'from_date' => date('F j, Y', strtotime($trip->from_date)),
            'to_date' => date('F j, Y', strtotime($trip->to_date)),
            'time' => $timeDifference, 
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
            $imageUrl = $user->profile_verified == 1 ? asset('storage/app/public/users/' . $user->profile) : '';
            $coverimageUrl = $user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $user->cover_img) : '';
            
        } else {
            $imageUrl = null; // Set default image URL if user not found
        }
       // Calculate time difference in hours
 $tripTime = Carbon::parse($trip->trip_datetime);
 $currentTime = Carbon::now();
 $hoursDifference = $tripTime->diffInHours($currentTime);
 
 // Determine the time display string
 if ($hoursDifference == 0) {
     $timeDifference = 'now';
 } elseif ($hoursDifference < 24) {
     $timeDifference = $hoursDifference . 'h';
 } else {
     $daysDifference = floor($hoursDifference / 24);
     $timeDifference = $daysDifference . 'd';
 }


        $tripimageUrl = asset('storage/app/public/trips/' . $trip->trip_image);

        $tripDetails[] = [
            'id' => $trip->id,
            'user_id' => $trip->user_id,
            'name' => $user->name,
            'verified' => $user->verified,
            'unique_name' => $user->unique_name,
            'profile' => $imageUrl,
            'cover_image' => $coverimageUrl,
            'trip_type' => $trip->trip_type,
            'from_date' => date('F j, Y', strtotime($trip->from_date)),
            'to_date' => date('F j, Y', strtotime($trip->to_date)),
            'time' => $timeDifference, 
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
    
 // Calculate time difference in hours
 $tripTime = Carbon::parse($trip->trip_datetime);
 $currentTime = Carbon::now();
 $hoursDifference = $tripTime->diffInHours($currentTime);
 
 // Determine the time display string
 if ($hoursDifference == 0) {
     $timeDifference = 'now';
 } elseif ($hoursDifference < 24) {
     $timeDifference = $hoursDifference . 'h';
 } else {
     $daysDifference = floor($hoursDifference / 24);
     $timeDifference = $daysDifference . 'd';
 }


    // Image URL
    $userProfileUrl = $user ? asset('storage/app/public/users/' . $user->profile) : null;
    $tripimageUrl = $trip ? asset('storage/app/public/trips/' . $trip->trip_image) : null;

    $tripDetails[] = [
        'id' => $trip->id,
        'user_id' => $trip->user_id,
        'name' => $user ? $user->name : 'Unknown',
        'verified' => $user->verified,
        'unique_name' => $user ? $user->unique_name : 'Unknown',
        'profile' => $userProfileUrl,
        'trip_type' => $trip->trip_type,
        'from_date' => date('F j, Y', strtotime($trip->from_date)),
        'to_date' => date('F j, Y', strtotime($trip->to_date)),
        'time' => $timeDifference, 
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

        // Check if user is trying to chat with themselves
        if ($user_id == $chat_user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot chat with yourself.',
            ], 400);
        }

        // Check if user and chat_user exist
        $user = Users::find($user_id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $chat_user = Users::find($chat_user_id);
        if (!$chat_user) {
            return response()->json([
                'success' => false,
                'message' => 'Chat user not found.',
            ], 404);
        }

        // Check if there's an existing chat between the user and chat_user
        $existingChat = Chats::where(function($query) use ($user_id, $chat_user_id) {
            $query->where('user_id', $user_id)
                ->where('chat_user_id', $chat_user_id);
        })
        ->orWhere(function($query) use ($user_id, $chat_user_id) {
            $query->where('user_id', $chat_user_id)
                ->where('chat_user_id', $user_id);
        })
        ->first();

    // Check if the chat is blocked
    if ($existingChat && $existingChat->chat_blocked == 1) {
    if ($existingChat->user_id == $user_id) {
    return response()->json([
    'success' => false,
    'message' => 'You have blocked this user.',
    ], 403);
    } else {
    return response()->json([
    'success' => false,
    'message' => 'You are blocked by this user.',
    ], 403);
    }
    }

    // Retrieve the gender of the chat user
    $userGender = $user->gender; // Assuming the gender field exists in the Users model
    // Check if chat_user's gender is not female
    if ($userGender !== 'female') {
        // If there's an existing chat, check the last update time
        if ($existingChat) {
            $lastUpdateTime = Carbon::parse($existingChat->datetime);
            $currentTime = Carbon::now();

            // If it's been more than an hour since the last update, deduct points
            if ($lastUpdateTime->diffInHours($currentTime) >= 1) {
                if ($user->points >= 10) {
                    $user->points -= 10;
                    if (!$user->save()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to update user points.',
                        ], 500);
                    }
                } else {
                    // User doesn't have sufficient points to chat
                    return response()->json([
                        'success' => false,
                        'message' => 'You don\'t have sufficient points to chat.',
                        'chat_status' => '0',
                    ], 400);
                }
            }
        } else {
            // It's a new chat, deduct points
            if ($user->points >= 10) {
                $user->points -= 10;
                if (!$user->save()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to update user points.',
                    ], 500);
                }
            } else {
                // User doesn't have sufficient points to chat
                return response()->json([
                    'success' => false,
                    'message' => 'You don\'t have sufficient points to chat.',
                    'chat_status' => '0',
                ], 400);
            }
        }
    }

    // If there's no existing chat, create new entries for both directions
    if (!$existingChat) {
        $currentTime = now();

        // Create first chat entry: user_id -> chat_user_id
        $newChat1 = new Chats();
        $newChat1->user_id = $user_id;
        $newChat1->chat_user_id = $chat_user_id;
        $newChat1->latest_message = $message;
        $newChat1->latest_msg_time = $currentTime;
        $newChat1->datetime = $currentTime;

        // Create second chat entry: chat_user_id -> user_id
        $newChat2 = new Chats();
        $newChat2->user_id = $chat_user_id;
        $newChat2->chat_user_id = $user_id;
        $newChat2->latest_message = $message;
        $newChat2->latest_msg_time = $currentTime;
        $newChat2->datetime = $currentTime;

        // Save both chat entries
        if (!$newChat1->save() || !$newChat2->save()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save Chat entries.',
            ], 500);
        }

            // Add notification entry
            $notification = new Notifications();
            $notification->user_id = $chat_user_id;
            $notification->notify_user_id = $user_id;
            $notification->message = 'New message arrived';
            $notification->save();

        // Return success response with new chat data
        return response()->json([
            'success' => true,
            'message' => 'Chat added successfully.',
            'chat_status' => '1',
            'data' => [[
                'chat1' => [
                    'id' => $newChat1->id,
                    'user_id' => $newChat1->user_id,
                    'chat_user_id' => $newChat1->chat_user_id,
                    'name' => $chat_user->name, 
                    'profile' => $chat_user->profile_verified == 1 ? asset('storage/app/public/users/' . $chat_user->profile) : '',
                    'cover_image' => $chat_user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $chat_user->cover_image) : '',
                    'latest_message' => $newChat1->latest_message,
                    'latest_msg_time' => Carbon::parse($newChat1->latest_msg_time)->format('Y-m-d H:i:s'),
                    'msg_seen' => '0',
                    'datetime' => Carbon::parse($newChat1->datetime)->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::parse($newChat1->updated_at)->format('Y-m-d H:i:s'),
                    'created_at' => Carbon::parse($newChat1->created_at)->format('Y-m-d H:i:s'),
                ],
                'chat2' => [
                    'id' => $newChat2->id,
                    'user_id' => $newChat2->user_id,
                    'chat_user_id' => $newChat2->chat_user_id,
                    'name' => $user->name,
                    'profile' => $user->profile_verified == 1 ? asset('storage/app/public/users/' . $user->profile) : '',
                    'cover_image' => $user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $user->cover_image) : '',
                    'latest_message' => $newChat2->latest_message,
                    'latest_msg_time' => Carbon::parse($newChat2->latest_msg_time)->format('Y-m-d H:i:s'),
                    'msg_seen' => '0',
                    'datetime' => Carbon::parse($newChat2->datetime)->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::parse($newChat2->updated_at)->format('Y-m-d H:i:s'),
                    'created_at' => Carbon::parse($newChat2->created_at)->format('Y-m-d H:i:s'),
            ],
            ]],
        ], 201);
    }

    // If an existing chat exists, update it
    $existingChat->latest_message = $message;
    $existingChat->latest_msg_time = now();
    $existingChat->datetime = now();
    if (!$existingChat->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update Chat.',
        ], 500);
    }
    // Add notification entry for existing chat
    $notification = new Notifications();
    $notification->user_id = $chat_user_id;
    $notification->notify_user_id = $user_id;
    $notification->message = 'New message arrived';
    $notification->save();
    
    // Return success response with updated chat data
    return response()->json([
        'success' => true,
        'message' => 'Chat updated successfully.',
        'chat_status' => '1',
        'data'=> [[
            'id' => $existingChat->id,
            'user_id' => $existingChat->user_id,
            'chat_user_id' => $existingChat->chat_user_id,
            'name' => $chat_user->name,
            'profile' => $chat_user->profile_verified == 1 ? asset('storage/app/public/users/' . $chat_user->profile) : '',
            'cover_image' => $chat_user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $chat_user->cover_image) : '',
            'latest_message' => $existingChat->latest_message,
            'latest_msg_time' => Carbon::parse($existingChat->latest_msg_time)->format('Y-m-d H:i:s'),
            'msg_seen' => '0',
            'datetime' => Carbon::parse($existingChat->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($existingChat->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($existingChat->created_at)->format('Y-m-d H:i:s'),
        ]],
    ], 200);
    }

public function chat_list(Request $request)
{
    // Get the user_id from the request
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

      // Fetching chats for the specific user_id, ordered by datetime
      $chats = Chats::where('user_id', $user_id)->orderBy('datetime', 'desc')->get();

    if ($chats->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No chats found.',
        ], 404);
    }

    $chatDetails = $chats->map(function ($chat) use ($user_id) {
        $chat_user = Users::find($chat->chat_user_id); // Fetch the chat_user details

        // Check if chat_user exists
        if (!$chat_user) {
            return null; // Skip this chat if user not found
        }

        $imageUrl = $chat_user->profile_verified == 1 ? asset('storage/app/public/users/' . $chat_user->profile) : '';
        $coverImageUrl = $chat_user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $chat_user->cover_img) : '';

              
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

           // Check if the user is a friend
           $isFriend = Friends::where('user_id', $user_id)
           ->where('friend_user_id', $chat->chat_user_id) // Check against notify_user_id
           ->exists();

       $friendStatus = $isFriend ? '1' : '0';  // Check if the user is a friend

        return [
            'id' => $chat->id,
            'user_id' => $chat->user_id,
            'chat_user_id' => $chat->chat_user_id,
            'name' => $chat_user->name, // Display chat_user name
            'profile' => $imageUrl, // Display chat_user profile
            'cover_img' => $coverImageUrl, // Display chat_user profile
            'online_status' => $chat_user->online_status, // Display chat_user online status
            'friend' => $friendStatus,
            'latest_message' => $chat->latest_message,
            'latest_msg_time' => $lastSeenFormatted,
            'msg_seen' => $chat->msg_seen,
            'datetime' => Carbon::parse($chat->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($chat->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($chat->created_at)->format('Y-m-d H:i:s'),
        ];
    })->filter(); // Remove null values from the collection

    return response()->json([
        'success' => true,
        'message' => 'Chat details listed successfully.',
        'data' => $chatDetails->values(), // Reindex the array to prevent gaps
    ], 200);
}

public function blocked_chat(Request $request)
{
    $user_id = $request->input('user_id');
    $chat_user_id = $request->input('chat_user_id');
    $chat_blocked = $request->input('chat_blocked');

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

    
    if ($chat_blocked === null) { // Check for null instead of empty
        return response()->json([
            'success' => false,
            'message' => 'chat_blocked is empty.',
        ], 400);
    }


    // Validate chat_blocked to ensure it's either 0 or 1
    if (!is_numeric($chat_blocked) || ($chat_blocked != 0 && $chat_blocked != 1)) {
        return response()->json([
            'success' => false,
            'message' => 'chat_blocked should be either 0 or 1.',
        ], 400);
    }

    // Check if the chat record exists
    $chat = Chats::where('user_id', $user_id)
                 ->where('chat_user_id', $chat_user_id)
                 ->first();

    if (!$chat) {
        return response()->json([
            'success' => false,
            'message' => 'Chat record not found.',
        ], 404);
    }

    // Update the chat_blocked field for the chat record
    $chat->chat_blocked = (int)$chat_blocked; // Ensure the value is an integer

    // Save the chat record
    if (!$chat->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save chat blocked status.',
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'Chat blocked status updated successfully.',
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
                'message' => 'Friend Removed successfully.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You Already Removed as Friend.',
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

        // Create a new friend instance
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
        $friendUserImageUrl = $friend_user->profile_verified == 1 ? asset('storage/app/public/users/' . $friend_user->profile) : '';
        $friendUserCoverImageUrl = $friend_user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $friend_user->cover_img) : '';

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Friend added successfully.',
            'data' => [
                'id' => $friend->id,
                'user_id' => $friend->user_id,
                'friend_user_id' => $friend_user->id,
                'name' => $friend_user->name,
                'profile' => $friendUserImageUrl,
                'cover_img' => $friendUserCoverImageUrl,
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
            'message' => 'No friends found.',
        ], 404);
    }

    $friendDetails = $friends->map(function ($friend) use ($user_id) {
        $user = $friend->user;
        $friendUser = $friend->friendUser;

        // Check if user and friendUser have latitude and longitude
        if (!empty($user->latitude) && !empty($user->longtitude) && !empty($friendUser->latitude) && !empty($friendUser->longtitude)) {
            // Calculate distance between user and friendUser
            $distance = $this->calculateDistance((float)$user->latitude, (float)$user->longtitude, (float)$friendUser->latitude, (float)$friendUser->longtitude);
            $distanceFormatted = round($distance, 2) . ' km'; // Round to 2 decimal places
        } else {
            $distanceFormatted = null; // Handle case where latitude or longitude is missing
        }

        $imageUrl = $friendUser->profile_verified == 1 ? asset('storage/app/public/users/' . $friendUser->profile) : '';
        $coverImageUrl = $friendUser->cover_img_verified == 1 ? asset('storage/app/public/users/' . $friendUser->cover_img) : '';

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

          // Check if the user is a friend
          $isFriend = Friends::where('user_id', $user_id)
          ->where('friend_user_id', $friendUser->id)
          ->exists();

      $friendStatus = $isFriend ? '1' : '0';  // Check if the user is a friend
      

        return [
            'id' => $friend->id,
            'user_id' => $friend->user_id,
            'friend_user_id' => $friend->friend_user_id,
            'name' => $friendUser->name,
            'introduction' => $friendUser->introduction,
            'gender' => $friendUser->gender,
            'age' => $friendUser->age,
            'online_status' => $friendUser->online_status,
            'friend' => $friendStatus,
            'profile' => $imageUrl,
            'cover_img' => $coverImageUrl,
            'last_seen' => $lastSeenFormatted,
            'distance' => isset($distanceFormatted) ? $distanceFormatted : null, // Distance between user and friend
            'status' => $friend->status == 1 ? 'Interested' : 'Not Interested',
            'datetime' => Carbon::parse($friend->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($friend->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($friend->created_at)->format('Y-m-d H:i:s'),
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Friends details listed successfully.',
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

    // Validate notify_user_id
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

    // Check if notify_user exists
    $notify_user = Users::find($notify_user_id);
    if (!$notify_user) {
        return response()->json([
            'success' => false,
            'message' => 'notify_user not found.',
        ], 404);
    }

    // Create a new notifications instance
    $notification = new Notifications();
    $notification->user_id = $user_id; 
    $notification->notify_user_id = $notify_user_id;
    $notification->message = $message; 
    $notification->datetime = now(); 

    // Save the notification
    if (!$notification->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save Notifications.',
        ], 500);
    }

    // Generate image URLs for user and notify_user
    //$userImageUrl = $user->profile_verified == 1 ? asset('storage/app/public/users/' . $user->profile) : '';
    $notifyUserImageUrl = $notify_user->profile_verified == 1 ? asset('storage/app/public/users/' . $notify_user->profile) : '';
    $notifyUserCoverImageUrl = $notify_user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $notify_user->cover_img) : '';

    // Return success response
    return response()->json([
        'success' => true,
        'message' => 'Notifications added successfully.',
        'data' => [
            'id' => $notification->id,
            'user_id' => $notification->user_id,
            'notify_user_id' => $notification->notify_user_id,
            'name' => $notify_user->name,
            'profile' => $notifyUserImageUrl,
            'cover_img' => $notifyUserCoverImageUrl,
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
            'message' => 'No notifications found.',
        ], 404);
    }

    // Prepare notification details
    $notificationDetails = $notifications->map(function ($notification) use ($user_id) {
        $user = Users::find($notification->user_id);
        $notify_user = Users::find($notification->notify_user_id);

        $imageUrl = $notify_user->profile_verified == 1 ? asset('storage/app/public/users/' . $notify_user->profile) : '';
        $coverImageUrl = $notify_user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $notify_user->cover_img) : '';
        //$notifyUserImageUrl = asset('storage/app/public/users/' . $notify_user->profile);
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
        // Check if the user is a friend
        $isFriend = Friends::where('user_id', $user_id)
            ->where('friend_user_id', $notification->notify_user_id) // Check against notify_user_id
            ->exists();

        $friendStatus = $isFriend ? '1' : '0';  // Check if the user is a friend

         
         return [
             'id' => $notification->id,
             'user_id' => $notification->user_id,
             'notify_user_id' => $notification->notify_user_id,
             'name' => $notify_user->name,
            'profile' => $imageUrl,
            'cover_img' => $coverImageUrl,
             'message' => $notification->message,
             'friend' => $friendStatus,
             'datetime' => $notificationTime->format('Y-m-d H:i:s'),
             'time' => $timeDifference,  // Add this line to include the time difference in hours
             'updated_at' => Carbon::parse($notification->updated_at)->format('Y-m-d H:i:s'),
             'created_at' => Carbon::parse($notification->created_at)->format('Y-m-d H:i:s'),
         ];
     });
 
     return response()->json([
         'success' => true,
         'message' => 'Notification details retrieved successfully.',
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
            'updated_at' => Carbon::parse($verification->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($verification->created_at)->format('Y-m-d H:i:s'),
            'selfie_image_url' => $selfieImageUrl,
            'front_image_url' => $frontImageUrl,
            'back_image_url' => $backImageUrl,
        ],
    ], 201);
}
public function add_points(Request $request)
{
    $user_id = $request->input('user_id'); 
    $points_id = $request->input('points_id');

    // Validate user_id
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    // Validate points_id
    if (empty($points_id)) {
        return response()->json([
            'success' => false,
            'message' => 'points_id is empty.',
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

    // Check if points entry exists
    $points_entry = Points::find($points_id);
    if (!$points_entry) {
        return response()->json([
            'success' => false,
            'message' => 'Points entry not found.',
        ], 404);
    }

    // Get points from the points entry
    $points = $points_entry->points;

    // Add points to the user's points field
    $user->points += $points;
    $user->total_points += $points;
    if (!$user->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update user points.',
        ], 500);
    }

    // Record the transaction
    $transaction = new Transaction();
    $transaction->user_id = $user_id;
    $transaction->points = $points;
    $transaction->type = 'add_points';
    $transaction->datetime = now();

    if (!$transaction->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save transaction.',
        ], 500);
    }
    $user = Users::select('name', 'points', 'total_points')->find($user_id);
    // Return success response
    return response()->json([
        'success' => true,
        'message' => 'Points added successfully.',
        'data' =>[$user],
    ], 201);
}
public function reward_points(Request $request)
{
    $user_id = $request->input('user_id'); 
    $points = $request->input('points');

    // Validate user_id
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    // Validate points
    if (empty($points)) {
        return response()->json([
            'success' => false,
            'message' => 'points is empty.',
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

    // Update user points
    $user->points += $points;
    $user->total_points += $points;
    
    if (!$user->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update user points.',
        ], 500);
    }

    // Record the transaction
    $transaction = new Transaction();
    $transaction->user_id = $user_id;
    $transaction->points = $points;
    $transaction->type = 'reward_points';
    $transaction->datetime = now();

    if (!$transaction->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save transaction.',
        ], 500);    
    }

    $user = Users::select('name', 'points', 'total_points')->find($user_id);
    // Return success response
    return response()->json([
        'success' => true,
        'message' => 'Reward Points added successfully.',
        'data' =>[$user],
    ], 201);
}

public function spin_points(Request $request)
{
    $user_id = $request->input('user_id'); 
    $points = $request->input('points');

    // Validate user_id
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    // Validate points
    if (empty($points)) {
        return response()->json([
            'success' => false,
            'message' => 'points is empty.',
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

    // Update user points
    $user->points += $points;
    $user->total_points += $points;
    
    if (!$user->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update user points.',
        ], 500);
    }

    // Record the transaction
    $transaction = new Transaction();
    $transaction->user_id = $user_id;
    $transaction->points = $points;
    $transaction->type = 'spin_points';
    $transaction->datetime = now();

    if (!$transaction->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save transaction.',
        ], 500);    
    }
    $user = Users::select('name', 'points', 'total_points')->find($user_id);
    // Return success response
    return response()->json([
        'success' => true,
        'message' => 'Spin Points added successfully.',
        'data' =>[$user],
    ], 201);
}

public function points_list(Request $request)
{
    // Fetch all points details from the database, ordered by price in descending order
    $points = Points::orderBy('points', 'desc')->get();

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

public function verify_front_image(Request $request)
{
    $userId = $request->input('user_id');

    if (empty($userId)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    $user = Users::find($userId);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 404);
    }

    $frontImage = $request->file('front_image');

    if ($frontImage) {
        $verification = Verifications::where('user_id', $userId)->first();

        if (!$verification) {
            $verification = new Verifications();
            $verification->user_id = $userId;
            $message = 'Front image added successfully.';
        } else {
            $message = 'Front image updated successfully.';
        }
        
        $imagePath = $frontImage->store('verification', 'public');
        $verification->front_image = basename($imagePath);
        $verification->save();

        return response()->json([
            'success' => true,
            'message' => $message,
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Front image is empty.',
        ], 400);
    }
}

public function verify_back_image(Request $request)
{
    $userId = $request->input('user_id');

    if (empty($userId)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    $user = Users::find($userId);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 404);
    }

    $backImage = $request->file('back_image');

    if ($backImage) {
        $verification = Verifications::where('user_id', $userId)->first();

        if (!$verification) {
            $verification = new Verifications();
            $verification->user_id = $userId;
            $message = 'back image added successfully.';
        } else {
            $message = 'back image updated successfully.';
        }
        
        $imagePath = $backImage->store('verification', 'public');
        $verification->back_image = basename($imagePath);
        $verification->save();

        return response()->json([
            'success' => true,
            'message' => $message,
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'back image is empty.',
        ], 400);
    }
}
public function verify_selfie_image(Request $request)
{
    $userId = $request->input('user_id');

    if (empty($userId)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    $user = Users::find($userId);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 404);
    }

    $selfieImage = $request->file('selfie_image');

    if ($selfieImage) {
        $verification = Verifications::where('user_id', $userId)->first();

        if (!$verification) {
            $verification = new Verifications();
            $verification->user_id = $userId;
            $message = 'selfie image added successfully.';
        } else {
            $message = 'selfie image updated successfully.';
        }
        
        $imagePath = $selfieImage->store('verification', 'public');
        $verification->selfie_image = basename($imagePath);
        $verification->save();

        return response()->json([
            'success' => true,
            'message' => $message,
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'selfie image is empty.',
        ], 400);
    }
}

public function add_feedback(Request $request)
{
    $user_id = $request->input('user_id'); 
    $feedbackContent = $request->input('feedback'); // Renamed the variable to avoid conflict

    // Validate user_id and feedbackContent
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    if (empty($feedbackContent)) {
        return response()->json([
            'success' => false,
            'message' => 'feedback is empty.',
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

    // Create a new Feedback instance
    $feedback = new Feedback();
    $feedback->user_id = $user_id; 
    $feedback->feedback = $feedbackContent; // Assign the feedback content to the model property

    // Save the feedback
    if (!$feedback->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save feedback.',
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'Feedback added successfully.',
    ], 201);
}

public function profession_list(Request $request)
{
    // Retrieve all professions
    $professions = Professions::all();

    if ($professions->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No profession found.',
        ], 404);
    }

    $professionData = [];
    foreach ($professions as $profession) {
        $professionData[] = [
            'id' => $profession->id,
            'profession' => $profession->profession,
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Professions listed successfully.',
        'data' => $professionData,
    ], 200);
}

public function settings_list(Request $request)
{
    // Retrieve all news settings
    $news = News::all();

    if ($news->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No settings found.',
        ], 404);
    }

    $newsData = [];
    foreach ($news as $item) {
        $newsData[] = [
            'id' => $item->id,
            'instagram_link' => $item->instagram,
            'telegram_link' => $item->telegram,
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Settings listed successfully.',
        'data' => $newsData,
    ], 200);
}


public function profile_view(Request $request)
{
    $user_id = $request->input('user_id'); 
    $profile_user_id = $request->input('profile_user_id'); // Renamed the variable to avoid conflict

    // Validate user_id and feedbackContent
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 400);
    }

    if (empty($profile_user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'profile_user_id is empty.',
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

        // Check if user exists
        $user = Users::find($profile_user_id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'profile_user_id not found.',
            ], 404);
        }

        // Add notification entry
        $notification = new Notifications();
        $notification->user_id = $profile_user_id;
        $notification->notify_user_id = $user_id;
        $notification->message = 'Profile Viewed Successfully';
        $notification->save();

    // Save the feedback
    if (!$notification->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save notification.',
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'Notification added successfully.',
    ], 201);
}

protected $oneSignalClient;

public function __construct(OneSignalClient $oneSignalClient)
{
    $this->oneSignalClient = $oneSignalClient;
}

public function send_notification(Request $request)
{
    $response = $this->oneSignalClient->sendNotificationToAll(
        "Some Message", 
        $url = null, 
        $data = null, 
        $buttons = null, 
        $schedule = null
    );

    return response()->json([
        'success' => true,
        'message' => 'Notification sent successfully.',
    ], 201);
}
}




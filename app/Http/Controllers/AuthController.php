<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users; 
use App\Models\Reports; 
use App\Models\Wallets; 
use App\Models\Withdrawals; 
use App\Models\BankDetails; 
use App\Models\Chats; 
use App\Models\Chat_points; 
use App\Models\Trips;
use App\Models\Friends; 
use App\Models\Points; 
use App\Models\Plans;
use App\Models\Notifications; 
use App\Models\Verifications; 
use App\Models\Transaction; 
use App\Models\Feedback;
use App\Models\Fakechats; 
use App\Models\AutoViewProfile; 
use App\Models\Professions; 
use App\Models\RechargeTrans;
use App\Models\VerificationTrans;
use App\Models\Appsettings; 
use App\Models\News; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Berkayk\OneSignal\OneSignalClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;


class AuthController extends Controller
{
 
    public function check_mobile(Request $request)
    {
        // Retrieve phone number from the request
        $mobile = $request->input('mobile');

        if (empty($mobile)) {
            $response['success'] = false;
            $response['message'] = 'mobile is empty.';
            return response()->json($response, 200);
        }
    
        // Remove non-numeric characters from the phone number
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
    
        // Check if the length of the phone number is not equal to 10
        if (strlen($mobile) !== 10) {
            $response['success'] = false;
            $response['message'] = "mobile number should be exactly 10 digits";
            return response()->json($response, 200);
        }
    
    
        // Check if a customer with the given phone number exists in the database
        $user = Users::where('mobile', $mobile)->first();
    
        // If customer not found, return failure response
        if (!$user) {
            $response['success'] = true;
            $response['registered'] = false;
            $response['message'] = 'mobile not registered.';
            return response()->json($response, 200);
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
        ], 200);
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid email format.',
        ], 200);
    }

    // Check if a customer with the given phone number exists in the database
    $user = Users::where('email', $email)->with('profession')->first();

    // If customer not found, return failure response
    if (!$user) {
        $response['success'] = true;
        $response['registered'] = false;
        $response['message'] = 'Email not registered.';
        return response()->json($response, 200);
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
        'profession' => $user->profession ? $user->profession->profession : null,
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
    $profession_id = $request->input('profession_id');
    $referred_by = $request->input('referred_by');
    $introduction = $request->input('introduction');
    $mobile = $request->input('mobile', '');

    $points = $request->input('points'); 
    $total_points = $request->input('total_points'); 

    $recharge_points = DB::table('news')
    ->orderBy('updated_at', 'desc') 
    ->value('recharge_points');

    $recharge_points = $recharge_points ?? 0;

    $points += $recharge_points;
    $total_points += $recharge_points;

    if (empty($state)) {
        return response()->json([
            'success' => false,
            'message' => 'state is empty.',
        ], 200);
    }
    if (empty($city)) {
        return response()->json([
            'success' => false,
            'message' => 'city is empty.',
        ], 200);
    }
    if (empty($introduction)) {
        return response()->json([
            'success' => false,
            'message' => 'introduction is empty.',
        ], 200);
    }
    if (empty($age)) {
        return response()->json([
            'success' => false,
            'message' => 'Age is empty.',
        ], 420);
    } elseif ($age < 18 || $age > 60) {
        return response()->json([
            'success' => false,
            'message' => 'Age should be between 18 and 60.',
        ], 200);
    }

    if (empty($name)) {
        return response()->json([
            'success' => false,
            'message' => 'Name is empty.',
        ], 200);
    } elseif (strlen($name) < 4 || strlen($name) > 18) {
        return response()->json([
            'success' => false,
            'message' => 'Name should be between 4 and 18 characters.',
        ], 200);
    }

    if (empty($gender)) {
        return response()->json([
            'success' => false,
            'message' => 'Gender is empty.',
        ], 200);
    } 
    if (empty($profession_id)) {
        return response()->json([
            'success' => false,
            'message' => 'profession_id is empty.',
        ], 200);
    }

    if (empty($email)) {
        return response()->json([
            'success' => false,
            'message' => 'Email is empty.',
        ], 200);
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid email format.',
        ], 200);
    }

    $existingUser = Users::where('unique_name', $unique_name)->first();
    if ($existingUser) {
        return response()->json([
            'success' => false,
            'message' => 'User already exists with this Unique Name.',
        ], 200);
    }
    $profession = Professions::find($profession_id);

    if (!$profession) {
        return response()->json([
            'success' => false,
            'message' => 'profession not found.',
        ], 200);
    }
    // Check if the user with the given email already exists
    $existingEmail = Users::where('email', $email)->first();
    if ($existingEmail) {
        return response()->json([
            'success' => false,
            'message' => 'User already exists with this email.',
        ], 200);
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
            ], 200);
        }
    }

    $user = new Users();
    $user->age = $age;
    $user->name = $name;
    $user->gender = $gender;
    $user->profession_id = $profession_id;
    $user->refer_code = $this->generateReferCode();
    $user->email = $email;
    $user->points = $points;
    $user->total_points = $total_points;
    $user->mobile = $mobile;
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

    Fakechats::create([
        'user_id' => $user->id,
        'status' => '0',
    ]);

        // Insert into auto_view_profile table
        $femaleUserIds = Users::where('gender', 'female')->inRandomOrder()->take(5)->pluck('id');

        $viewDatetime = now();
        $increments = [5, 7, 16, 25, 52]; // minutes to add for each record

        foreach ($femaleUserIds as $index => $femaleUserId) {
            AutoViewProfile::create([
                'user_id' => $user_id,
                'view_user_id' => $femaleUserId,
                'view_datetime' => $viewDatetime->copy()->addMinutes($increments[$index]),
            ]);
    
            // Update the viewDatetime for the next entry
            $viewDatetime = $viewDatetime->addMinutes($increments[$index]);
        }

    $user->load('profession');

         // Add notification entries for female users
         if ($gender === 'male') {
            $this->addNotificationsForFemaleUsers($user_id, "{$user->name} registered In App.");
        }


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
            'mobile' => $user->mobile ?? '',
            'age' => $user->age,
            'gender' => $user->gender,
            'state' => $user->state,
            'city' => $user->city,
            'profession' => $user->profession ? $user->profession->profession : null,
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
private function addNotificationsForFemaleUsers($newUserId, $message)
{
    // Fetch female users
    $femaleUserIds = Users::where('gender', 'female')->pluck('id');

    foreach ($femaleUserIds as $femaleUserId) {
        // Store notification entry for each female user
        $notification = new Notifications();
        $notification->user_id = $femaleUserId;
        $notification->notify_user_id = $newUserId;  // ID of the new user
        $notification->message = $message;
        $notification->datetime = now();
        $notification->save();
    }

    //Send notifications to all female users
   foreach ($femaleUserIds as $femaleUserId) {
       $this->sendNotificationsToFemaleUser(strval($femaleUserId), $message);
   }
}

protected function sendNotificationsToFemaleUser($femaleUserId, $message)
{
    // Fetch the user
    $user = Users::find($femaleUserId);

    if ($user) {  // Send notification only if the user is offline
        // Send notification via OneSignal
        $this->oneSignalClient->sendNotificationToExternalUser(
            $message,
            $femaleUserId, // Corrected to use $femaleUserId
            $url = null,
            $data = null,
            $buttons = null,
            $schedule = null
        );
    }
}

public function userdetails(Request $request)
{
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    // Fetch the user details from the database based on the provided user_id
    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    $online_status = $request->input('online_status');

    // Check if the online_status is being updated (either '0' or '1')
    if ($online_status === '0' || $online_status === '1') {
        $user->online_status = $online_status;
        $user->active_datetime = Carbon::now(); 
        $user->save();
    }

    $user->load('profession');

    // Get the sum of unread values
    $unreadMessagesSum = Chats::where('user_id', $user_id)
        ->where('unread', '>', 0)  // Assuming 'unread' is a numeric field
        ->sum('unread');

    // Image URLs
    $imageUrl = $user->profile ? asset('storage/app/public/users/' . $user->profile) : '';
    $coverimageUrl = $user->cover_img ? asset('storage/app/public/users/' . $user->cover_img) : '';
    $selfiimageUrl = $user->selfi_image ? asset('storage/app/public/users/' . $user->selfi_image) : '';
    $proofimageUrl = $user->proof_image ? asset('storage/app/public/users/' . $user->proof_image) : '';

    return response()->json([
        'success' => true,
        'message' => 'User details retrieved successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'unique_name' => $user->unique_name,
            'email' => $user->email,
            'mobile' => $user->mobile ?? '',
            'age' => $user->age,
            'gender' => $user->gender,
            'state' => $user->state,
            'city' => $user->city,
            'profession' => $user->profession ? $user->profession->profession : '',
            'refer_code' => $user->refer_code,
            'referred_by' => $user->referred_by,
            'profile' => $imageUrl,
            'cover_img' => $coverimageUrl,
            'points' => $user->points,
            'verified' => $user->verified,
            'online_status' => $user->online_status, // Updated value
            'introduction' => $user->introduction,
            'message_notify' => $user->message_notify,
            'add_friend_notify' => $user->add_friend_notify,
            'view_notify' => $user->view_notify,
            'profile_verified' => $user->profile_verified,
            'cover_img_verified' => $user->cover_img_verified,
            'latitude' => $user->latitude,
            'longtitude' => $user->longtitude,
            'balance' => $user->balance ?? '',
            'selfi_image' => $selfiimageUrl,
            'proof_image' => $proofimageUrl,
            'unread_count' => strval($unreadMessagesSum), // Cast unread count to string
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
    $other_user_id = $request->input('other_user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }
    if (empty($other_user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'other_user_id is empty.',
        ], 200);
    }

    // Fetch the other user's details from the database based on the provided other_user_id
    $otherUser = Users::find($other_user_id);

    if (!$otherUser) {
        return response()->json([
            'success' => false,
            'message' => 'Other user not found.',
        ], 200);
    }

    $otherUser->load('profession');

    // Image URLs
    $imageUrl = $otherUser->profile_verified == 1 ? asset('storage/app/public/users/' . $otherUser->profile) : '';
    $coverImageUrl = $otherUser->cover_img_verified == 1 ? asset('storage/app/public/users/' . $otherUser->cover_img) : '';

    // Check if the other user is a friend
    $isFriend = Friends::where(function ($query) use ($user_id, $other_user_id) {
        $query->where('user_id', $user_id)
              ->where('friend_user_id', $other_user_id);
    })
    ->exists();

    return response()->json([
        'success' => true,
        'message' => 'Other user details retrieved successfully.',
        'data' => [
            'id' => $otherUser->id,
            'name' => $otherUser->name,
            'unique_name' => $otherUser->unique_name,
            'email' => $otherUser->email,
            'mobile' => $otherUser->mobile,
            'age' => $otherUser->age,
            'gender' => $otherUser->gender,
            'state' => $otherUser->state,
            'city' => $otherUser->city,
            'profession' => $otherUser->profession ? $otherUser->profession->profession : '',
            'refer_code' => $otherUser->refer_code,
            'referred_by' => $otherUser->referred_by ? $otherUser->referred_by->referred_by : '',
            'profile' => $imageUrl,
            'cover_img' => $coverImageUrl,
            'points' => $otherUser->points,
            'verified' => $otherUser->verified,
            'online_status' => $otherUser->online_status,
            'introduction' => $otherUser->introduction,
            'message_notify' => $otherUser->message_notify,
            'add_friend_notify' => $otherUser->add_friend_notify,
            'view_notify' => $otherUser->view_notify,
            'profile_verified' => $otherUser->profile_verified,
            'cover_img_verified' => $otherUser->cover_img_verified,
            'balance' => $otherUser->balance ?? '',
            'last_seen' => Carbon::parse($otherUser->last_seen)->format('Y-m-d H:i:s'),
            'datetime' => Carbon::parse($otherUser->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($otherUser->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($otherUser->created_at)->format('Y-m-d H:i:s'),
            'friend_status' => $isFriend ? '1' : '0', // Add friend status to response
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
        ], 200);
    }

    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    if (!$request->hasFile('profile')) {
        return response()->json([
            'success' => false,
            'message' => 'Profile image is empty.',
        ], 200);
    }

    $profile = $request->file('profile');

    if ($profile->isValid()) {
        $imagePath = $profile->store('users', 'public');
        $user->profile = basename($imagePath);
        $user->profile_verified = 1; 
        $user->datetime = now(); 
        $user->save();

        $user->load('profession');

        $imageUrl = asset('storage/app/public/users/' . $user->profile);
        $coverimageUrl = asset('storage/app/public/users/' . $user->cover_img);

        return response()->json([
            'success' => true,
            'message' => 'User profile updated successfully.',
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
                'profession' => $user->profession ? $user->profession->profession : null,
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
            'message' => 'Invalid profile image file.',
        ], 200);
    }
} 


public function update_cover_img(Request $request)
{
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 200);
    }

    $cover_img = $request->file('cover_img');

    if ($cover_img !== null) {
        $imagePath = $cover_img->store('users', 'public');
        $user->cover_img = basename($imagePath);
        $user->cover_img_verified = 1;
        $user->datetime = now(); 
        $user->save();
        // Image URL
        $user->load('profession');
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
               'profession' => $user->profession ? $user->profession->profession : '',
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
            'message' => 'Cover img is empty.',
        ], 200);
    }
}


public function update_users(Request $request)
{
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 200);
    }

    $name = $request->input('name');
    $email = $request->input('email');
    $unique_name = $request->input('unique_name');
    $age = $request->input('age');
    $profession_id = $request->input('profession_id');
    $state = $request->input('state');
    $city = $request->input('city');
    $introduction = $request->input('introduction');

    // Validate age
    if ($age !== null) {
        if ($age < 18 || $age > 60) {
            return response()->json([
                'success' => false,
                'message' => 'Age should be between 18 and 60.',
            ], 200);
        }
    }

    // Validate name
    if ($name !== null) {
        if (strlen($name) < 4 || strlen($name) > 18) {
            return response()->json([
                'success' => false,
                'message' => 'Name should be between 4 and 18 characters.',
            ], 200);
        }
    }

    // Validate email
    if ($email !== null) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email format.',
            ], 200);
        }
    }

    // Validate profession_id
    if ($profession_id !== null) {
        $profession = Professions::find($profession_id);
        if (!$profession) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid profession_id.',
            ], 200);
        }
    }

    // Update user details
    if ($name !== null) {
        $user->name = $name;
    }
    if ($email !== null) {
        $user->email = $email;
    }
    if ($age !== null) {
        $user->age = $age;
    }
    if ($profession_id !== null) {
        $user->profession_id = $profession_id;
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

    $user->load('profession');

    // Image URL
    $imageUrl = asset('storage/app/public/users/' . $user->profile);
    $coverimageUrl = asset('storage/app/public/users/' . $user->cover_img);

    return response()->json([
        'success' => true,
        'message' => 'User details updated successfully.',
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
            'profession' => $user->profession ? $user->profession->profession : null,
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
        ], 200);
    }

    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 200);
    }

    $latitude = $request->input('latitude');
    $longtitude = $request->input('longtitude');

    if (is_null($latitude)) {
        return response()->json([
            'success' => false,
            'message' => 'latitude is empty.',
        ], 200);
    }

    if (is_null($longtitude)) {
        return response()->json([
            'success' => false,
            'message' => 'longtitude is empty.',
        ], 200);
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
        ], 200);
    }

    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 200);
    }

    $message_notify = $request->input('message_notify');
    $add_friend_notify = $request->input('add_friend_notify');
    $view_notify = $request->input('view_notify');

    if (is_null($message_notify)) {
        return response()->json([
            'success' => false,
            'message' => 'Message Notify is empty.',
        ], 200);
    }

    if (is_null($add_friend_notify)) {
        return response()->json([
            'success' => false,
            'message' => 'Add Friend Notify is empty.',
        ], 200);
    }

    if (is_null($view_notify)) {
        return response()->json([
            'success' => false,
            'message' => 'View Notify is empty.',
        ], 200);
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
    $profile_image = $request->input('profile_image'); // New field for profile image

    // Validate each input and return specific error messages
    if (empty($trip_type)) {
        return response()->json([
            'success' => false,
            'message' => 'Trip Type is empty.',
        ], 200);
    }
    if (empty($from_date)) {
        return response()->json([
            'success' => false,
            'message' => 'From Date is empty.',
        ], 200);
    }
    if (empty($to_date)) {
        return response()->json([
            'success' => false,
            'message' => 'To Date is empty.',
        ], 200);
    }
    if (empty($trip_title)) {
        return response()->json([
            'success' => false,
            'message' => 'Trip Title is empty.',
        ], 200);
    }
    if (empty($trip_description)) {
        return response()->json([
            'success' => false,
            'message' => 'Trip Description is empty.',
        ], 200);
    }
    if (empty($location)) {
        return response()->json([
            'success' => false,
            'message' => 'Location is empty.',
        ], 200);
    }
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'User ID is empty.',
        ], 200);
    }

    // Check if the user exists
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    // Check if the user already has a pending trip
    $pendingTrip = Trips::where('user_id', $user_id)
        ->where('trip_status', 0) // Assuming 0 means pending
        ->exists();

    if ($pendingTrip) {
        return response()->json([
            'success' => false,
            'message' => 'You already have a pending trip. Please wait until it is approved before adding a new one.',
        ], 200);
    }

        // Check if the user is verified and gender is male
        if ($user->gender == 'male' && $user->verified != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Please Verify your profile then post trip.',
            ], 200);
           }


    // Handle profile image URL and save it to the trips folder if needed
    if ($profile_image == 1 && !empty($user->profile)) {
        // Get the profile image file path
        $profileImagePath = storage_path('app/public/users/' . $user->profile);

        // Check if the file exists
        if (file_exists($profileImagePath)) {
            // Define the new path for the trip image
            $newTripImagePath = 'trips/' . $user->profile;
            $newTripImageFullPath = storage_path('app/public/' . $newTripImagePath);

            // Copy the profile image to the trips folder
            copy($profileImagePath, $newTripImageFullPath);

            // Set the trip image basename
            $trip_image = basename($user->profile);
        } else {
            // Handle the case where the profile image file does not exist
            $trip_image = null;
        }
    } else {
        $trip_image = null; // Set to null or handle differently if needed
    }

    // Create a new trip instance
    $trip = new Trips();
    $trip->user_id = $user_id;
    $trip->trip_type = $trip_type;
    $trip->from_date = Carbon::parse($from_date)->format('Y-m-d');
    $trip->to_date = Carbon::parse($to_date)->format('Y-m-d');
    $trip->trip_title = $trip_title;
    $trip->trip_description = $trip_description;
    $trip->location = $location;
    $trip->trip_datetime = now();
    $trip->trip_image = $trip_image; // Set trip_image to the new path
    $trip->save();

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
            'trip_image' => $imageUrl, // Return the image URL
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
        ], 200);
    }

    // Fetch the trip
    $trip = Trips::find($tripId);

    if (!$trip) {
        return response()->json([
            'success' => false,
            'message' => 'Trip not found.',
        ], 200);
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
        ], 200);
    }
}

public function update_trip(Request $request)
{
    $trip_id = $request->input('trip_id');

    if (empty($trip_id)) {
        return response()->json([
            'success' => false,
            'message' => 'trip_id is empty.',
        ], 200);
    }

    // Retrieve the trip
    $trip = Trips::find($trip_id);

    if (!$trip) {
        return response()->json([
            'success' => false,
            'message' => 'Trip not found.',
        ], 200);
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
            ], 200);
        }
        $trip->user_id = $user_id;
    }
    if ($trip_type !== null) {
        if (empty($trip_type)) {
            return response()->json([
                'success' => false,
                'message' => 'Trip Type is empty.',
            ], 200);
        }
        $trip->trip_type = $trip_type;
    }
    if ($from_date !== null) {
        if (empty($from_date)) {
            return response()->json([
                'success' => false,
                'message' => 'From Date is empty.',
            ], 200);
        }
        $trip->from_date = Carbon::parse($from_date)->format('Y-m-d');
    }
    if ($to_date !== null) {
        if (empty($to_date)) {
            return response()->json([
                'success' => false,
                'message' => 'To Date is empty.',
            ], 200);
        }
        $trip->to_date = Carbon::parse($to_date)->format('Y-m-d');
    }
    if ($trip_title !== null) {
        if (empty($trip_title)) {
            return response()->json([
                'success' => false,
                'message' => 'Trip Title is empty.',
            ], 200);
        }
        $trip->trip_title = $trip_title;
    }
    if ($trip_description !== null) {
        if (empty($trip_description)) {
            return response()->json([
                'success' => false,
                'message' => 'Trip Description is empty.',
            ], 200);
        }
        $trip->trip_description = $trip_description;
    }
 
    if ($location !== null) {
        if (empty($location)) {
            return response()->json([
                'success' => false,
                'message' => 'Location is empty.',
            ], 200);
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
        ], 200);
    }

    $userId = $request->input('user_id');

    $userExists = Users::find($userId);
    if (!$userExists) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid User ID.',
        ], 200);
    }

    // Get user latitude and longitude
    $userLatitude = (float)$userExists->latitude;
    $userLongitude = (float)$userExists->longitude;

    // Validate type
    if (!$request->has('type')) {
        return response()->json([
            'success' => false,
            'message' => 'Type is required.',
        ], 200);
    }

    $type = $request->input('type');

    // Get offset and limit from request with default values
    $offset = $request->has('offset') ? $request->input('offset') : 0; // Default offset is 0 if not provided
    $limit = $request->has('limit') ? $request->input('limit') : 10; // Default limit is 10 if not provided

    // Validate offset
    if (!is_numeric($offset)) {
        return response()->json([
            'success' => false,
            'message' => 'Offset is empty.',
        ], 200);
    }

    // Validate limit
    if (!is_numeric($limit)) {
        return response()->json([
            'success' => false,
            'message' => 'Limit is empty.',
        ], 200);
    }

    // Convert offset and limit to integers
    $offset = (int)$offset;
    $limit = (int)$limit;

    $currentDate = Carbon::now()->toDateString();
    $tripsQuery = Trips::where('trip_status', 1)
                ->join('users', 'trips.user_id', '=', 'users.id')
                ->select('trips.*');

    if ($type == 'latest') {
        $totalTrips = $tripsQuery->count();
        if ($offset >= $totalTrips) {
            $offset = 0;
        }
        $trips = $tripsQuery->orderBy('trip_datetime', 'desc')
                            ->skip($offset)
                            ->take($limit)
                            ->get();
    } elseif ($type == 'nearby') {
        $allTrips = $tripsQuery->get(); // Fetch all trips for nearby calculation
        $totalTrips = count($allTrips);
        if ($offset >= $totalTrips) {
            $offset = 0;
        }
        $trips = $allTrips;
    } elseif ($type == 'date') {
        if (!$request->has('date')) {
            return response()->json([
                'success' => false,
                'message' => 'Date is required.',
            ], 200);
        }
        $fromDate = $request->input('date');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format. Expected format: YYYY-MM-DD.',
            ], 200);
        }

        $totalTrips = $tripsQuery->whereDate('from_date', $fromDate)->count();
        if ($offset >= $totalTrips) {
            $offset = 0;
        }
        $trips = $tripsQuery->whereDate('from_date', $fromDate)
                            ->skip($offset)
                            ->take($limit)
                            ->get();
    } elseif ($type == 'female') {
        // Filter trips by gender 'female'
        $tripsQuery->whereHas('users', function ($query) {
            $query->where('gender', 'female');
        });

        $totalTrips = $tripsQuery->count();
        $trips = $tripsQuery->skip($offset)
                            ->take($limit)
                            ->get();
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Invalid type provided',
        ], 200);
    }

    if ($trips->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No trips found.',
        ], 200);
    }

    $tripsWithDistance = [];
    foreach ($trips as $trip) {
        $tripUser = Users::find($trip->user_id);

        if (!$tripUser) {
            continue;
        }

        $distance = $this->calculateDistance($userLatitude, $userLongitude, (float)$tripUser->latitude, (float)$tripUser->longitude);
        $tripsWithDistance[] = [
            'trip' => $trip,
            'user' => $tripUser,
            'distance' => round($distance)
        ];
    }
    if ($type == 'nearby') {
        if (empty($tripsWithDistance)) {
            return response()->json([
                'success' => false,
                'message' => 'No trips found.',
            ], 200);
        }

        usort($tripsWithDistance, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        // Apply offset and limit after sorting
        if ($offset >= count($tripsWithDistance)) {
            $offset = 0;
        }
        $tripsWithDistance = array_slice($tripsWithDistance, $offset, $limit);
    }

    if (empty($tripsWithDistance)) {
        return response()->json([
            'success' => false,
            'message' => 'No trips found.',
        ], 200);
    }

    $tripDetailsFormatted = [];
    foreach ($tripsWithDistance as $detail) {
        $trip = $detail['trip'];
        $user = $detail['user'];
        $distance = $detail['distance'];

        $imageUrl = $user->profile_verified == 1 ? asset('storage/app/public/users/' . $user->profile) : '';
        $coverImageUrl = $user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $user->cover_img) : '';

        $isFriend = Friends::where('user_id', $userId)
                          ->where('friend_user_id', $user->id)
                          ->exists();
        $friendStatus = $isFriend ? '1' : '0';

        $tripTime = Carbon::parse($trip->trip_datetime);
        $currentTime = Carbon::now();
        $hoursDifference = $tripTime->diffInHours($currentTime);
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
            'distance' => round($distance) . ' km'
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Trip details retrieved successfully.',
        'total' => $totalTrips,
        'data' => $tripDetailsFormatted,
    ], 200);
}

private function calculateDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
{
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $latDiff = $latTo - $latFrom;
    $lonDiff = $lonTo - $lonFrom;

    $a = sin($latDiff / 2) * sin($latDiff / 2) + cos($latFrom) * cos($latTo) * sin($lonDiff / 2) * sin($lonDiff / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c;
}

public function my_trip_list(Request $request)
{
    // Get the user_id from the request
    $user_id = $request->input('user_id');

    // Get offset and limit from request with default values
    $offset = $request->has('offset') ? $request->input('offset') : 0; // Default offset is 0 if not provided
    $limit = $request->has('limit') ? $request->input('limit') : 10; // Default limit is 10 if not provided

    // Validate offset
    if (!is_numeric($offset)) {
        return response()->json([
            'success' => false,
            'message' => 'Offset is empty.',
        ], 200);
    }

    // Validate limit
    if (!is_numeric($limit)) {
        return response()->json([
            'success' => false,
            'message' => 'Limit is empty.',
        ], 200);
    }

    // Convert offset and limit to integers
    $offset = (int)$offset;
    $limit = (int)$limit;

    $totalTrips = Trips::where('user_id', $user_id)->count();

          // If offset is beyond the total chats, set offset to 0
          if ($offset >= $totalTrips) {
            $offset = 0;
        } 

    // Fetch trips for the specific user_id from the database with pagination
    $trips = Trips::where('user_id', $user_id)
        ->skip($offset)
        ->take($limit)
        ->get();



    if ($trips->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No trips found.',
        ], 200);
    }

    $tripDetails = [];

    foreach ($trips as $trip) {
        $user = Users::find($trip->user_id);
        if ($user) {
            $imageUrl = asset('storage/app/public/users/' . $user->profile);
            $coverimageUrl = asset('storage/app/public/users/' . $user->cover_img);
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
        'total' => $totalTrips,
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
        ], 200);
    }

    // Check if the date is in a valid format (YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid date format. Expected format: YYYY-MM-DD.',
        ], 200);
    }

    // Fetch trips for the specific date from the database, comparing only the date part of the datetime field
    $trips = Trips::whereDate('trip_datetime', $date)->get();

    if ($trips->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No trips found for this date.',
        ], 200);
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
        ], 200);
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
        ], 200);
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


public function recent_trip(Request $request)
{
    // Fetch all recent trips with pagination
    $offset = $request->input('offset', 0); // Default offset is 0 if not provided
    $limit = $request->input('limit', 10);  // Default limit is 10 if not provided

    // Validate offset
    if (!is_numeric($offset) || $offset < 0) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid offset value.',
        ], 200);
    }

    // Validate limit
    if (!is_numeric($limit) || $limit <= 0) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid limit value.',
        ], 200);
    }

    $tripsQuery = Trips::orderBy('created_at', 'desc');

    // Get the total count of trips for pagination
    $totalTrips = $tripsQuery->count();

    // Apply pagination
    $trips = $tripsQuery->skip($offset)->take($limit)->get();

    if ($trips->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No trips found.',
        ], 200);
    }

    // Assuming the current user's latitude and longtitude are available in the request
    $userLatitude = (float) $request->input('user_latitude', 0); // Replace with actual method to get current user's latitude
    $userLongtitude = (float) $request->input('user_longtitude', 0); // Replace with actual method to get current user's longtitude

    // Calculate distance function
    $calculateDistance = function ($lat1, $long1, $lat2, $long2) {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        $latFrom = deg2rad($lat1);
        $longFrom = deg2rad($long1);
        $latTo = deg2rad($lat2);
        $longTo = deg2rad($long2);

        $latDelta = $latTo - $latFrom;
        $longDelta = $longTo - $longFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                                cos($latFrom) * cos($latTo) *
                                pow(sin($longDelta / 2), 2)));

        return $earthRadius * $angle;
    };

    $tripDetails = $trips->map(function ($trip) use ($userLatitude, $userLongtitude, $calculateDistance) {
        $user = Users::find($trip->user_id);

        // Calculate time difference in hours
        $tripTime = Carbon::parse($trip->trip_datetime);
        $currentTime = Carbon::now();
        $hoursDifference = $tripTime->diffInHours($currentTime);

        // Determine the time display string
        $timeDifference = $hoursDifference == 0 ? 'now' :
                          ($hoursDifference < 24 ? $hoursDifference . 'h' :
                          floor($hoursDifference / 24) . 'd');

        // Calculate distance if user coordinates are available
        $distance = $user && $user->latitude && $user->longtitude
                    ? $calculateDistance($userLatitude, $userLongtitude, (float)$user->latitude, (float)$user->longtitude)
                    : null;

        // Image URLs
        $userProfileUrl = $user ? asset('storage/app/public/users/' . $user->profile) : null;
        $tripImageUrl = $trip ? asset('storage/app/public/trips/' . $trip->trip_image) : null;

        return [
            'id' => $trip->id,
            'user_id' => $trip->user_id,
            'name' => $user ? $user->name : 'Unknown',
            'verified' => $user ? $user->verified : false,
            'unique_name' => $user ? $user->unique_name : 'Unknown',
            'profile' => $userProfileUrl,
            'trip_type' => $trip->trip_type,
            'from_date' => Carbon::parse($trip->from_date)->format('F j, Y'),
            'to_date' => Carbon::parse($trip->to_date)->format('F j, Y'),
            'time' => $timeDifference,
            'trip_title' => $trip->trip_title,
            'trip_description' => $trip->trip_description,
            'location' => $trip->location,
            'trip_status' => $trip->trip_status,
            'trip_image' => $tripImageUrl,
            'trip_datetime' => Carbon::parse($trip->trip_datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($trip->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($trip->created_at)->format('Y-m-d H:i:s'),
            'distance' => $distance ? round($distance) . ' km' : null
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Trip details retrieved successfully.',
        'total' => $totalTrips,
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
        ], 200);
    }

    // Fetch the offer from the database based on the provided offer_id
    $trip = Trips::find($trip_id);

    if (!$trip) {
        return response()->json([
            'success' => false,
            'message' => 'trip not found.',
        ], 200);
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
    $unread = $request->input('unread');

    // Validate inputs
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    if (empty($chat_user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'chat_user_id is empty.',
        ], 200);
    }

    if (empty($message)) {
        return response()->json([
            'success' => false,
            'message' => 'Message is empty.',
        ], 200);
    }

    if (!isset($unread)) {
        return response()->json([
            'success' => false,
            'message' => 'unread is not provided.',
        ], 200);
    }

    // Check for self-chat
    if ($user_id == $chat_user_id) {
        return response()->json([
            'success' => false,
            'message' => 'You cannot chat with yourself.',
        ], 200);
    }

    // Validate users
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    $chat_user = Users::find($chat_user_id);
    if (!$chat_user) {
        return response()->json([
            'success' => false,
            'message' => 'Chat user not found.',
        ], 200);
    }

    // Check if chat is blocked
    $existingChat = Chats::where('user_id', $user_id)
                          ->where('chat_user_id', $chat_user_id)
                          ->first();

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

    // Gender and points check
    $pointsRequired = 10;
    $userGender = $user->gender;
    $currentTime = Carbon::now();
    

    if ($userGender !== 'female') {
        // Check for last points deduction
        $lastChatPoints = Chat_points::where('user_id', $user_id)
                                     ->where('chat_user_id', $chat_user_id)
                                     ->latest('datetime')
                                     ->first();

        if ($lastChatPoints) {
            $lastUpdateTime = Carbon::parse($lastChatPoints->datetime);
            // If less than an hour has passed, skip points deduction
            if ($lastUpdateTime->diffInHours($currentTime) < 1) {
                // Skip points deduction and return updated chat
                if ($existingChat) {
                    $existingChat->msg_seen = 1;
                    $existingChat->latest_message = $message;
                    $existingChat->latest_msg_time = $currentTime;
                    $existingChat->datetime = $currentTime;
                    if (!$existingChat->save()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to update Chat.',
                        ], 500);
                    }

                    // Add notification entry
                    $notification = new Notifications();
                    $notification->user_id = $chat_user_id;
                    $notification->notify_user_id = $user_id;
                    $notification->message = "{$user->name}, messaged you";
                    $notification->datetime = $currentTime;
                    $notification->save();
                    
                    $this->sendNotificationsToUser(strval($chat_user_id), "{$user->name} messaged you");

                    // Update reverse chat entry if it exists
                        $reverseChat = Chats::where('user_id', $chat_user_id)
                        ->where('chat_user_id', $user_id)
                        ->first();

                        if ($reverseChat) {
                            // Update unread count for the reverse chat
                            $reverseChat->unread = ($unread == 1 || $unread == 0) ? $reverseChat->unread + $unread : $reverseChat->unread;
                            if (!$reverseChat->save()) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Failed to update reverse chat.',
                                ], 500);
                            }
                        }

                    // Construct the link for existing chat
                    $chatLink = "https://www.dudeways.com/path/to/userid?userid=" . strval($chat_user_id) . '&chatid=' . strval($user_id);

                    // Return success response with updated chat data
                    return response()->json([
                        'success' => true,
                        'message' => 'Chat updated successfully.',
                        'chat_status' => '1',
                        'data' => [[
                            'chat_status' => '1',
                            'id' => $existingChat->id,
                            'user_id' => $existingChat->user_id,
                            'chat_user_id' => $existingChat->chat_user_id,
                            'name' => $chat_user->name,
                            'profile' => $chat_user->profile_verified == 1 ? asset('storage/app/public/users/' . $chat_user->profile) : '',
                            'cover_image' => $chat_user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $chat_user->cover_image) : '',
                            'latest_message' => $existingChat->latest_message,
                            'latest_msg_time' => $currentTime->format('Y-m-d H:i:s'),
                            'msg_seen' => $existingChat->msg_seen,
                            'unread' => strval($existingChat->unread), // Cast unread count to string
                            'datetime' => $currentTime->format('Y-m-d H:i:s'),
                            'updated_at' => $currentTime->format('Y-m-d H:i:s'),
                            'created_at' => Carbon::parse($existingChat->created_at)->format('Y-m-d H:i:s'),
                            'link' => $chatLink, // Include the link
                            ]],
                    ], 200);
                }
            }
        }

        if ($user->points >= $pointsRequired) {
            $user->points -= $pointsRequired;
            if (!$user->save()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user points.',
                ], 500);
            }

           // Check if the chat user is female
           if ($chat_user->gender === 'female') {
                $pointsToCredit = floor($pointsRequired * 0.60);
              $chat_user->balance += $pointsToCredit;
    

            // Save the female user's updated balance
            if (!$chat_user->save()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update female user balance.',
                ], 500);
            }

        // Check if the female user was referred by someone
        if (!empty($chat_user->referred_by)) {
            // Find all users that were referred by the same refer_code
            $referrers = Users::where('refer_code', $chat_user->referred_by)->get(); 
            
            // Loop through each referrer and credit 10% of the points to their balance
            foreach ($referrers as $referrer) {
                $referrerPointsToCredit = floor($pointsRequired * 0.10); // Calculate 10% of the points
                $referrer->balance += $referrerPointsToCredit;

                // Save each referrer's updated balance
                if (!$referrer->save()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to update referrer balance.',
                    ], 500);
                }

                // Record the transaction for the referrer
                $transaction = new Transaction();
                $transaction->user_id = $referrer->id; // Set the referrer's user ID
                $transaction->amount = $referrerPointsToCredit; // Set the credited amount
                $transaction->points = 0; // Set the credited amount
                $transaction->type = 'refer_amount'; // Specify transaction type
                $transaction->datetime = now(); // Record the current timestamp

                // Save the transaction
                if (!$transaction->save()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to record transaction for referrer.',
                    ], 500);
                }
            }
        }
    }


            // Create a new chat points record
            $chat_points = new Chat_points();
            $chat_points->user_id = $user_id;
            $chat_points->chat_user_id = $chat_user_id;
            $chat_points->points = $pointsRequired;
            $chat_points->datetime = $currentTime;
            if (!$chat_points->save()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save chat points.',
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You don\'t have sufficient points to chat.',
                'chat_status' => '0',
            ], 200);
        }
    }

    if ($existingChat) {
        // Update the existing chat entry
        $existingChat->msg_seen = 1;
        $existingChat->latest_message = $message;
        $existingChat->latest_msg_time = $currentTime;
        $existingChat->datetime = $currentTime;
       

        if (!$existingChat->save()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Chat.',
            ], 500);
        }

        // Add notification entry
        $notification = new Notifications();
        $notification->user_id = $chat_user_id;
        $notification->notify_user_id = $user_id;
        $notification->message = "{$user->name}, messaged you";
        $notification->datetime = $currentTime;
        $notification->save();
        
        $this->sendNotificationsToUser(strval($chat_user_id), "{$user->name} messaged you");


    // Update reverse chat entry if it exists
    $reverseChat = Chats::where('user_id', $chat_user_id)
    ->where('chat_user_id', $user_id)
    ->first();

    if ($reverseChat) {
        // Update unread count for the reverse chat
        $reverseChat->unread = ($unread == 1 || $unread == 0) ? $reverseChat->unread + $unread : $reverseChat->unread;
        if (!$reverseChat->save()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update reverse chat.',
            ], 500);
        }
    }
 // Construct the link for existing chat
 $chatLink = "https://www.dudeways.com/path/to/userid?userid=" . strval($chat_user_id) . '&chatid=' . strval($user_id);


        // Return success response with updated chat data
        return response()->json([
            'success' => true,
            'message' => 'Chat updated successfully.',
            'chat_status' => '1',
            'data' => [[
                'chat_status' => '1',
                'id' => $existingChat->id,
                'user_id' => $existingChat->user_id,
                'chat_user_id' => $existingChat->chat_user_id,
                'name' => $chat_user->name,
                'profile' => $chat_user->profile_verified == 1 ? asset('storage/app/public/users/' . $chat_user->profile) : '',
                'cover_image' => $chat_user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $chat_user->cover_image) : '',
                'latest_message' => $existingChat->latest_message,
                'latest_msg_time' => $currentTime->format('Y-m-d H:i:s'),
                'msg_seen' => $existingChat->msg_seen,
                'unread' => strval($existingChat->unread), // Cast unread count to string
                'datetime' => $currentTime->format('Y-m-d H:i:s'),
                'updated_at' => $currentTime->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($existingChat->created_at)->format('Y-m-d H:i:s'),
                'link' => $chatLink, // Include the link
                ]],
        ], 200);
    }

    // If no existing chat, create both directions
    $currentTime = now();

    // Create the chat entry for user_id to chat_user_id
    $newChat1 = new Chats();
    $newChat1->user_id = $user_id;
    $newChat1->chat_user_id = $chat_user_id;
    $newChat1->latest_message = $message;
    $newChat1->unread = 0; // Assuming this user has seen the message
    $newChat1->msg_seen = 1; // Assuming this user has seen the message
    $newChat1->latest_msg_time = $currentTime;
    $newChat1->datetime = $currentTime;
    
    // Create the chat entry for chat_user_id to user_id
    $newChat2 = new Chats();
    $newChat2->user_id = $chat_user_id;
    $newChat2->chat_user_id = $user_id;
    $newChat2->latest_message = $message;
    $newChat2->unread = $unread;
    $newChat2->msg_seen = 0; // Assuming this user has not seen the message yet
    $newChat2->latest_msg_time = $currentTime;
    $newChat2->datetime = $currentTime;

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
    $notification->message = "{$user->name}, messaged you";
    $notification->datetime = now();
    $notification->save();
    
    $this->sendNotificationsToUser(strval($chat_user_id), "{$user->name} messaged you");

    // Return success response with new chat data
    return response()->json([
        'success' => true,
        'message' => 'Chat added successfully.',
        'chat_status' => '1',
        'data' => [[
            'chat_status' => '1',
            'chat1' => [
                'id' => $newChat1->id,
                'user_id' => $newChat1->user_id,
                'chat_user_id' => $newChat1->chat_user_id,
                'name' => $chat_user->name,
                'profile' => $chat_user->profile_verified == 1 ? asset('storage/app/public/users/' . $chat_user->profile) : '',
                'cover_image' => $chat_user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $chat_user->cover_image) : '',
                'latest_message' => $newChat1->latest_message,
                'latest_msg_time' => Carbon::parse($newChat1->latest_msg_time)->format('Y-m-d H:i:s'),
                'msg_seen' => '1',
                'unread' => strval($newChat1->unread), // Cast unread count to string
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
                'unread' => strval($newChat2->unread), // Cast unread count to string
                'datetime' => Carbon::parse($newChat2->datetime)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($newChat2->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($newChat2->created_at)->format('Y-m-d H:i:s'),
            ],
        ]],
    ], 201);
}

        protected function sendNotificationsToUser($chat_user_id, $message)
            {
                $user = Users::find($chat_user_id);

                
                if ($user && $user->online_status == 0) {
                    
                    // Send notification via OneSignal
                    $this->oneSignalClient->sendNotificationToExternalUser(
                        $message,
                        $chat_user_id,
                        $url = 'https://www.dudeways.com/path/to/userid=' . strval($chat_user_id) . '&chatid=' . strval($user->user_id) . '&senderName=' . urlencode($user->name) . '&receiverName=' . urlencode($user->name) . '&senderUniqueName=' . urlencode($user->unique_name) . '&receiverName=' . urlencode($user->name),
                        $data = null,
                        $buttons = null,
                        $schedule = null
                    );
                }
            }


    
    public function chat_list(Request $request)
    {
        // Get the user_id from the request
        $user_id = $request->input('user_id');
    
        if (empty($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'user_id is empty.',
            ], 200);
        }
    
        // Get offset and limit from request with default values
        $offset = $request->has('offset') ? $request->input('offset') : 0;
        $limit = $request->has('limit') ? $request->input('limit') : 15;
    
        // Validate offset and limit
        if (!is_numeric($offset) || !is_numeric($limit)) {
            return response()->json([
                'success' => false,
                'message' => 'Offset or limit is empty.',
            ], 200);
        }
    
        // Convert offset and limit to integers
        $offset = (int)$offset;
        $limit = (int)$limit;
    
        // Fetch total count of chats for the specific user_id
        $totalChats = Chats::where('user_id', $user_id)->count();
    
        // If offset is beyond the total chats, set offset to 0
        if ($offset >= $totalChats) {
            $offset = 0;
        }
    
        // Fetch chats for the specific user_id with ordering by latest_msg_time
        $chats = Chats::where('user_id', $user_id)
            ->orderBy('latest_msg_time', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();
    
        if ($chats->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No chats found.',
                'total' => 0,
                'chat_status' => '0',
            ], 200);
        }
        $unreadMessagesSum = Chats::where('user_id', $user_id)
        ->where('unread', '>', 0)  // Assuming 'unread' is a numeric field
        ->sum('unread');
    
        // Prepare chat details
        $chatDetails = $chats->map(function ($chat) use ($user_id) {
            $chat_user = Users::find($chat->chat_user_id);
    
            if (!$chat_user) {
                return null;
            }
    
            $imageUrl = $chat_user->profile_verified == 1 ? asset('storage/app/public/users/' . $chat_user->profile) : '';
            $coverImageUrl = $chat_user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $chat_user->cover_img) : '';
    
            // Check if the user is a friend
            $isFriend = Friends::where('user_id', $user_id)
                ->where('friend_user_id', $chat->chat_user_id)
                ->exists();
    
            $friendStatus = $isFriend ? '1' : '0';
    
            // Fetch the latest message from both perspectives
            $latestChatMessage = Chats::where('user_id', $chat->chat_user_id)
                ->where('chat_user_id', $user_id)
                ->orderBy('datetime', 'desc')
                ->first(['latest_message', 'datetime']);
    
            $latestUserMessage = Chats::where('user_id', $user_id)
                ->where('chat_user_id', $chat->chat_user_id)
                ->orderBy('datetime', 'desc')
                ->first(['latest_message', 'datetime']);
    
            // Determine the most recent datetime
            $latestMsgTime = null;
            if ($latestChatMessage && $latestUserMessage) {
                $latestMsgTime = Carbon::parse($latestChatMessage->datetime)->greaterThan(Carbon::parse($latestUserMessage->datetime)) 
                    ? Carbon::parse($latestChatMessage->datetime) 
                    : Carbon::parse($latestUserMessage->datetime);
            } elseif ($latestChatMessage) {
                $latestMsgTime = Carbon::parse($latestChatMessage->datetime);
            } elseif ($latestUserMessage) {
                $latestMsgTime = Carbon::parse($latestUserMessage->datetime);
            } else {
                $latestMsgTime = Carbon::parse($chat->latest_msg_time);
            }
    
            // Determine the display format for latest_msg_time
            $now = Carbon::now();
            $msgDifferenceDays = $now->diffInDays($latestMsgTime);
    
            if ($msgDifferenceDays == 0) {
                $latestMsgTimeFormatted = $latestMsgTime->format('g:i A');
            } elseif ($msgDifferenceDays == 1) {
                $latestMsgTimeFormatted = 'Yesterday';
            } elseif ($msgDifferenceDays <= 7) {
                $latestMsgTimeFormatted = $latestMsgTime->format('l');
            } elseif ($msgDifferenceDays <= 14 && $latestMsgTime->isSameMonth($now)) {
                $latestMsgTimeFormatted = 'Last week';
            } elseif ($latestMsgTime->month == $now->subMonths(1)->month) {
                $latestMsgTimeFormatted = 'Last month';
            } elseif ($latestMsgTime->isSameYear($now)) {
                $latestMsgTimeFormatted = $latestMsgTime->format('M jS');
            } else {
                $latestMsgTimeFormatted = $latestMsgTime->format('M jS, Y');
            }
    
            return [
                'chat_status' => '1',
                'id' => $chat->id,
                'user_id' => $chat->user_id,
                'chat_user_id' => $chat->chat_user_id,
                'name' => $chat_user->name,
                'unique_name' => $chat_user->unique_name,
                'points' => $chat_user->points,
                'profile' => $imageUrl,
                'cover_img' => $coverImageUrl,
                'online_status' => $chat_user->online_status,
                'verified' => $chat_user->verified,
                'friend' => $friendStatus,
                'latest_message' => $latestChatMessage && $latestUserMessage ? ($latestMsgTime->isSameMinute(Carbon::parse($latestChatMessage->datetime)) ? $latestChatMessage->latest_message : $latestUserMessage->latest_message) : ($latestChatMessage ? $latestChatMessage->latest_message : $latestUserMessage->latest_message),
                'latest_msg_time' => $latestMsgTimeFormatted,
                'latest_msg_time_display' =>$latestMsgTime->format('Y-m-d H:i:s'),
                'msg_seen' => strval($chat->msg_seen),
                'unread' => strval($chat->unread),
                'datetime' => Carbon::parse($chat->datetime)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($chat->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($chat->created_at)->format('Y-m-d H:i:s'),
            ];
        })->filter();
    
        // Sort chat details by actual datetime to ensure the most recent ones come first
        $sortedChatDetails = $chatDetails->sortByDesc(function($chat) {
            return Carbon::parse($chat['latest_msg_time_display']);
        })->values()->all();
    
        return response()->json([
            'success' => true,
            'message' => 'Chat details listed successfully.',
            'total' => $totalChats,
            'unread_count' => strval($unreadMessagesSum), // Cast unread count to string
            'data' => $sortedChatDetails, // Return the sorted chat details
        ], 200);
    }
    
    
    public function read_chats(Request $request)
    {
        $user_id = $request->input('user_id');
        $chat_user_id = $request->input('chat_user_id');
        $msg_seen = $request->input('msg_seen');
    
        // Validate user_id
        if (empty($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'user_id is empty.',
            ], 200);
        }
    
        // Validate chat_user_id
        if (empty($chat_user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'chat_user_id is empty.',
            ], 200);
        }
    
        // Update unread field for the first scenario
        $updatedUnreadChats = Chats::where('user_id', $user_id)
                                   ->where('chat_user_id', $chat_user_id)
                                   ->get();
    
        foreach ($updatedUnreadChats as $chat) {
            $chat->unread = 0;
            if (!$chat->save()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update unread status for some chats.',
                ], 500);
            }
        }
    
        // Update msg_seen field for the second scenario
        if (isset($msg_seen) && in_array($msg_seen, [0, 1])) {
            $updatedMsgSeenChats = Chats::where('user_id', $chat_user_id)
                                        ->where('chat_user_id', $user_id)
                                        ->get();
    
            foreach ($updatedMsgSeenChats as $chat) {
                $chat->msg_seen = $msg_seen;
                if (!$chat->save()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to update msg_seen status for some chats.',
                    ], 500);
                }
            }
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Read Chats updated successfully.',
        ], 200);
    }
    
public function delete_chat(Request $request)
{
    $user_id = $request->input('user_id');
    $chat_user_id = $request->input('chat_user_id');

    // Validate user_id
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    // Validate chat_user_id
    if (empty($chat_user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'chat_user_id is empty.',
        ], 200);
    }

    // Find the chat entries where user_id and chat_user_id match in either direction
    $chats = Chats::where(function($query) use ($user_id, $chat_user_id) {
                    $query->where('user_id', $user_id)
                          ->where('chat_user_id', $chat_user_id);
                })
                ->orWhere(function($query) use ($user_id, $chat_user_id) {
                    $query->where('user_id', $chat_user_id)
                          ->where('chat_user_id', $user_id);
                })
                ->get();

    // Check if chats exist
    if ($chats->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Chat not found.',
        ], 200);
    }

    // Delete the chats
    foreach ($chats as $chat) {
        if (!$chat->delete()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Chat.',
            ], 500);
        }
    }

    // Return success response
    return response()->json([
        'success' => true,
        'message' => 'Chat deleted successfully.',
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
        ], 200);
    }

    // Validate chat_user_id
    if (empty($chat_user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'chat_user_id is empty.',
        ], 200);
    }

    
    if ($chat_blocked === null) { // Check for null instead of empty
        return response()->json([
            'success' => false,
            'message' => 'chat_blocked is empty.',
        ], 200);
    }


    // Validate chat_blocked to ensure it's either 0 or 1
    if (!is_numeric($chat_blocked) || ($chat_blocked != 0 && $chat_blocked != 1)) {
        return response()->json([
            'success' => false,
            'message' => 'chat_blocked should be either 0 or 1.',
        ], 200);
    }

    // Check if the chat record exists
    $chat = Chats::where('user_id', $user_id)
                 ->where('chat_user_id', $chat_user_id)
                 ->first();

    if (!$chat) {
        return response()->json([
            'success' => false,
            'message' => 'Chat record not found.',
        ], 200);
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
        ], 200);
    }

    // Validate friend_user_id
    if (empty($friend_user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'friend_user_id is empty.',
        ], 200);
    }

    // Check if user_id and friend_user_id are the same
    if ($user_id == $friend_user_id) {
        return response()->json([
            'success' => false,
            'message' => 'You cannot add yourself as a friend.',
        ], 200);
    }

    // Validate friend action
    if (!isset($friend)) {
        return response()->json([
            'success' => false,
            'message' => 'friend is empty.',
        ], 200);
    }

    // Check if user exists
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 200);
    }

    // Check if friend_user exists
    $friend_user = Users::find($friend_user_id);
    if (!$friend_user) {
        return response()->json([
            'success' => false,
            'message' => 'friend_user not found.',
        ], 200);
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
            ], 200);
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
            ], 200);
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
        ], 200);
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
        ], 200);
    }
  // Get offset and limit from request with default values
  $offset = $request->has('offset') ? $request->input('offset') : 0; // Default offset is 0 if not provided
  $limit = $request->has('limit') ? $request->input('limit') : 10; // Default limit is 10 if not provided

  // Validate offset
  if (!is_numeric($offset)) {
      return response()->json([
          'success' => false,
          'message' => 'Offset is empty.',
      ], 200);
  }

  // Validate limit
  if (!is_numeric($limit)) {
      return response()->json([
          'success' => false,
          'message' => 'Limit is empty.',
      ], 200);
  }

  // Convert offset and limit to integers
  $offset = (int)$offset;
  $limit = (int)$limit;
  
 
     // Fetch friends for the specific user_id from the database with pagination
     $friendsQuery = Friends::where('user_id', $user_id);
     $totalFriends = $friendsQuery->count(); // Get total count of friends
     
     if ($offset >= $totalFriends) {
        $offset = 0;
    }

     $friends = $friendsQuery->skip($offset)
         ->take($limit)
         ->get();


    if ($friends->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No friends found.',
        ], 200);
    }

    $friendDetails = $friends->map(function ($friend) use ($user_id) {
        $user = $friend->user;
        $friendUser = $friend->friendUser;

        // Check if user and friendUser have latitude and longitude
        if (!empty($user->latitude) && !empty($user->longtitude) && !empty($friendUser->latitude) && !empty($friendUser->longtitude)) {
            // Calculate distance between user and friendUser
            $distance = $this->calculateDistance((float)$user->latitude, (float)$user->longtitude, (float)$friendUser->latitude, (float)$friendUser->longtitude);
            $distanceFormatted = round($distance) . ' km'; // Round to the nearest whole number
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
            'unique_name' => $friendUser->unique_name,
            'verified' => strval($friendUser->verified), // Cast verified string
            'introduction' => $friendUser->introduction,
            'gender' => $friendUser->gender,
            'age' => $friendUser->age,
            'online_status' => $friendUser->online_status,
            'friend' => $friendStatus,
            'profile' => $imageUrl,
            'cover_img' => $coverImageUrl,
            'last_seen' => $lastSeenFormatted,
            'distance' => isset($distanceFormatted) ? $distanceFormatted :"", // Distance between user and friend
            'status' => $friend->status == 1 ? 'Interested' : 'Not Interested',
            'datetime' => Carbon::parse($friend->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($friend->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($friend->created_at)->format('Y-m-d H:i:s'),
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Friends details listed successfully.',
        'total' => $totalFriends,
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
        ], 200);
    }

    // Validate notify_user_id
    if (empty($notify_user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'notify_user_id is empty.',
        ], 200);
    }

    // Validate message
    if (empty($message)) {
        return response()->json([
            'success' => false,
            'message' => 'Message is empty.',
        ], 200);
    }

    // Check if user exists
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 200);
    }

    // Check if notify_user exists
    $notify_user = Users::find($notify_user_id);
    if (!$notify_user) {
        return response()->json([
            'success' => false,
            'message' => 'notify_user not found.',
        ], 200);
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
        ], 200);
    }

     // Get offset and limit from request with default values
  $offset = $request->has('offset') ? $request->input('offset') : 0; // Default offset is 0 if not provided
  $limit = $request->has('limit') ? $request->input('limit') : 10; // Default limit is 10 if not provided

  // Validate offset
  if (!is_numeric($offset)) {
      return response()->json([
          'success' => false,
          'message' => 'Offset is empty.',
      ], 200);
  }

  // Validate limit
  if (!is_numeric($limit)) {
      return response()->json([
          'success' => false,
          'message' => 'Limit is empty.',
      ], 200);
  }

  // Convert offset and limit to integers
  $offset = (int)$offset;
  $limit = (int)$limit;
    $totalNotifications = Notifications::where('user_id', $user_id)->count();


    if ($offset >= $totalNotifications) {
        $offset = 0;
    }

    // Fetch notifications for the specific user_id from the database with pagination
    $notifications = Notifications::where('user_id', $user_id)
        ->orderBy('datetime', 'desc')
        ->skip($offset)
        ->take($limit)
        ->get();
    

    if ($notifications->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No notifications found.',
        ], 200);
    }

    // Prepare notification details
    $notificationDetails = $notifications->map(function ($notification) use ($user_id) {
        $notify_user = Users::find($notification->notify_user_id);

        $imageUrl = $notify_user->profile_verified == 1 ? asset('storage/app/public/users/' . $notify_user->profile) : '';
        $coverImageUrl = $notify_user->cover_img_verified == 1 ? asset('storage/app/public/users/' . $notify_user->cover_img) : '';

        // Calculate time difference
        $notificationTime = Carbon::parse($notification->datetime);
        $currentTime = Carbon::now();
        $hoursDifference = $notificationTime->diffInHours($currentTime);
        $daysDifference = $notificationTime->diffInDays($currentTime);

        // Determine the time display string
        if ($daysDifference == 0) {
            $timeDifference = $notificationTime->format('H:i'); // Today, show time
        } elseif ($daysDifference == 1) {
            $timeDifference = 'Yesterday'; // Yesterday
        } elseif ($daysDifference <= 7) {
            $timeDifference = $notificationTime->format('l'); // Last week, show day name
        } elseif ($daysDifference <= 14 && $notificationTime->isSameMonth($currentTime)) {
            $timeDifference = 'Last week'; // Within 14 days and same month, show "Last week"
        } elseif ($notificationTime->month == $currentTime->subMonth()->month) {
            $timeDifference = 'Last month'; // Last month
        } elseif ($notificationTime->isSameYear($currentTime)) {
            $timeDifference = $notificationTime->format('M jS'); // This year, show month and day with ordinal indicator
        } else {
            $timeDifference = $notificationTime->format('M jS, Y'); // Older than current year, show month, day, and year
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
            'unique_name' => $notify_user->unique_name,
            'verified' => strval($notify_user->verified), // Cast verified string
            'profile' => $imageUrl,
            'cover_img' => $coverImageUrl,
            'message' => $notification->message,
            'friend' => $friendStatus,
            'datetime' => $notificationTime->format('Y-m-d H:i:s'),
            'time' => $timeDifference,
            'updated_at' => Carbon::parse($notification->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($notification->created_at)->format('Y-m-d H:i:s'),
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Notification details retrieved successfully.',
        'total' => $totalNotifications, 
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
        ], 200);
    }
    if (empty($selfie_image)) {
        return response()->json([
            'success' => false,
            'message' => 'Selfie Image is empty.',
        ], 200);
    }
    if (empty($front_image)) {
        return response()->json([
            'success' => false,
            'message' => 'Front Image is empty.',
        ], 200);
    }
    if (empty($back_image)) {
        return response()->json([
            'success' => false,
            'message' => 'Back Image is empty.',
        ], 200);
    }

    // Check if user exists
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
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
        ], 200);
    }

    // Validate points_id
    if (empty($points_id)) {
        return response()->json([
            'success' => false,
            'message' => 'points_id is empty.',
        ], 200);
    }

    // Check if user exists
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    // Check if points entry exists
    $points_entry = Points::find($points_id);
    if (!$points_entry) {
        return response()->json([
            'success' => false,
            'message' => 'Points entry not found.',
        ], 200);
    }

    // Get points from the points entry
    $points = $points_entry->points;
    $price = $points_entry->price;

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
    $transaction->amount = $price;
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
        'data' => [
            'name' => $user->name,
            'points' => (string) $user->points,
            'total_points' => (string) $user->total_points,
        ],
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
        ], 200);
    }

    // Validate points
    if (empty($points)) {
        return response()->json([
            'success' => false,
            'message' => 'points is empty.',
        ], 200);
    }

    // Check if user exists
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
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
        'data' => [
            'name' => $user->name,
            'points' => (string) $user->points,
            'total_points' => (string) $user->total_points,
        ],
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
        ], 200);
    }

    // Validate points
    if (empty($points)) {
        return response()->json([
            'success' => false,
            'message' => 'points is empty.',
        ], 200);
    }

    // Check if user exists
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    // Check if user can add points (once per day)
    $latestTransaction = Transaction::where('user_id', $user_id)
                                     ->where('type', 'spin_points')
                                     ->orderBy('datetime', 'desc')
                                     ->first();
    $currentTimestamp = Carbon::now();

    if ($latestTransaction) {
        $lastSpinTime = Carbon::parse($latestTransaction->datetime);
        $diffInSeconds = $currentTimestamp->diffInSeconds($lastSpinTime);

        // Check if less than 24 hours have passed
        if ($diffInSeconds < 86400) { // 86400 seconds = 24 hours
            $remainingSeconds = 86400 - $diffInSeconds;
            $hoursLeft = floor($remainingSeconds / 3600);
            $minutesLeft = floor(($remainingSeconds % 3600) / 60);
            
            $timeLeftMessage = "You have $hoursLeft hours and $minutesLeft minutes left to spin the points.";
            return response()->json([
                'success' => false,
                'message' => $timeLeftMessage,
            ], 200);
        }
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
    $transaction->datetime = $currentTimestamp;

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
        'data' => [
            'name' => $user->name,
            'points' => (string) $user->points,
            'total_points' => (string) $user->total_points,
        ],
    ], 201);
}

public function points_list(Request $request)
{
    $points = Points::orderBy('points', 'desc')->get();

    if ($points->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No Points found.',
        ], 200);
    }

    $pointsDetails = [];

    foreach ($points as $point) {
        $pointsDetails[] = [
            'id' => $point->id,
            'points' => (string) $point->points,
            'offer_percentage' => (string) $point->offer_percentage,
            'price' => (string) $point->price,
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
        ], 200);
    }

    $user = Users::find($userId);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    if ($user->verified == 1) {
        return response()->json([
            'success' => false,
            'message' => 'User already verified.',
        ], 403); 
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

        // Store the front image
        $imagePath = $frontImage->store('verification', 'public');
        $verification->front_image = basename($imagePath);
        $verification->save();

        // Fetch the updated verification details
        $updatedVerification = Verifications::where('user_id', $userId)->first();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'id' => $updatedVerification->id,
                'user_id' => $updatedVerification->user_id,
                'front_image' => asset('storage/app/public/verification/' . $updatedVerification->front_image),
                'status' => $updatedVerification->status,
                'payment_status' => $updatedVerification->payment_status,
                'updated_at' => Carbon::parse($updatedVerification->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($updatedVerification->created_at)->format('Y-m-d H:i:s'),
            ],
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Front image is empty.',
        ], 200);
    }
}

public function verify_back_image(Request $request)
{
    $userId = $request->input('user_id');

    if (empty($userId)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    $user = Users::find($userId);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    if ($user->verified == 1) {
        return response()->json([
            'success' => false,
            'message' => 'User already verified.',
        ], 403); 
    }

    $backImage = $request->file('back_image');

    if ($backImage) {
        $verification = Verifications::where('user_id', $userId)->first();

        if (!$verification) {
            $verification = new Verifications();
            $verification->user_id = $userId;
            $message = 'Back image added successfully.';
        } else {
            $message = 'Back image updated successfully.';
        }
        
        // Store the back image
        $imagePath = $backImage->store('verification', 'public');
        $verification->back_image = basename($imagePath);
        $verification->save();

        // Fetch the updated verification details
        $updatedVerification = Verifications::where('user_id', $userId)->first();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'id' => $updatedVerification->id,
                'user_id' => $updatedVerification->user_id,
                'back_image' => asset('storage/app/public/verification/' . $updatedVerification->back_image),
                'status' => $updatedVerification->status,
                'payment_status' => $updatedVerification->payment_status,
                'updated_at' => Carbon::parse($updatedVerification->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($updatedVerification->created_at)->format('Y-m-d H:i:s'),
            ],
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Back image is empty.',
        ], 200);
    }
}

public function verify_selfie_image(Request $request)
{
    $userId = $request->input('user_id');

    if (empty($userId)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    $user = Users::find($userId);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    if ($user->verified == 1) {
        return response()->json([
            'success' => false,
            'message' => 'User already verified.',
        ], 403); 
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
        
        // Store the selfie image
        $imagePath = $selfieImage->store('verification', 'public');
        $verification->selfie_image = basename($imagePath);
        $verification->save();

        // Fetch the updated verification details
        $updatedVerification = Verifications::where('user_id', $userId)->first();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [[
                'id' => $updatedVerification->id,
                'user_id' => $updatedVerification->user_id,
                'selfie_image' => asset('storage/app/public/verification/' . $updatedVerification->selfie_image),
                'status' => $updatedVerification->status,
                'payment_status' => $updatedVerification->payment_status,
                'updated_at' => Carbon::parse($updatedVerification->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($updatedVerification->created_at)->format('Y-m-d H:i:s'),
            ]],
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'selfie image is empty.',
        ], 200);
    }
}

public function payment_image(Request $request)
{
    $userId = $request->input('user_id');

    if (empty($userId)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    $user = Users::find($userId);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    if ($user->verified == 1) {
        return response()->json([
            'success' => false,
            'message' => 'User already verified.',
        ], 403); 
    }

    $paymentImage = $request->file('payment_image');

    if ($paymentImage) {
        $verification = Verifications::where('user_id', $userId)->first();

        if (!$verification) {
            $verification = new Verifications();
            $verification->user_id = $userId;
            $message = 'Payment image added successfully.';
        } else {
            $message = 'Verification Sent for review Successfully.';
        }
        
        // Store the selfie image
        $imagePath = $paymentImage->store('verification', 'public');
        $verification->payment_image = basename($imagePath);
        $verification->save();

        // Fetch the updated verification details
        $updatedVerification = Verifications::where('user_id', $userId)->first();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [[
                'id' => $updatedVerification->id,
                'user_id' => $updatedVerification->user_id,
                'payment_image' => asset('storage/app/public/verification/' . $updatedVerification->payment_image),
                'status' => $updatedVerification->status,
                'payment_status' => $updatedVerification->payment_status,
                'updated_at' => Carbon::parse($updatedVerification->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($updatedVerification->created_at)->format('Y-m-d H:i:s'),
            ]],
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Payment image is empty.',
        ], 200);
    }
}

public function verification_list(Request $request)
{
    // Get the user_id from the request
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    // Get offset and limit from request with default values
    $offset = $request->has('offset') ? $request->input('offset') : 0; // Default offset is 0 if not provided
    $limit = $request->has('limit') ? $request->input('limit') : 10; // Default limit is 10 if not provided

    // Validate offset
    if (!is_numeric($offset) || $offset < 0) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid offset.',
        ], 200);
    }

    // Validate limit
    if (!is_numeric($limit) || $limit <= 0) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid limit.',
        ], 200);
    }

    // Convert offset and limit to integers
    $offset = (int)$offset;
    $limit = (int)$limit;

    $totalVerifications = Verifications::where('user_id', $user_id)->count();

    if ($offset >= $totalVerifications) {
        $offset = 0;
    }

    // Fetch verifications for the specific user_id from the database with pagination
    $verifications = Verifications::where('user_id', $user_id)
        ->skip($offset)
        ->take($limit)
        ->get();

    if ($verifications->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No verifications found.',
        ], 200);
    }

    // Format the verification details
    $verificationsDetails = $verifications->map(function ($verification) {
        $selfieImageUrl = $verification->selfie_image ? asset('storage/app/public/verification/' . $verification->selfie_image) : '';
        $frontImageUrl = $verification->front_image ? asset('storage/app/public/verification/' . $verification->front_image) : '';
        $backImageUrl = $verification->back_image ? asset('storage/app/public/verification/' . $verification->back_image) : '';
        $PaymentImageUrl = $verification->payment_image ? asset('storage/app/public/verification/' . $verification->payment_image) : '';

        return [
            'id' => $verification->id,
            'user_id' => $verification->user_id,
            'selfie_image' => $selfieImageUrl,
            'front_image' => $frontImageUrl,
            'back_image' => $backImageUrl,
            'payment_image' => $PaymentImageUrl,
            'status' => $verification->status,
            'payment_status' => $verification->payment_status,
            'updated_at' => Carbon::parse($verification->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($verification->created_at)->format('Y-m-d H:i:s'),
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Verifications details retrieved successfully.',
        'total' => $totalVerifications,
        'data' => $verificationsDetails,
    ], 200);
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
        ], 200);
    }

    if (empty($feedbackContent)) {
        return response()->json([
            'success' => false,
            'message' => 'feedback is empty.',
        ], 200);
    }

    // Check if user exists
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 200);
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
        ], 200);
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
        ], 200);
    }

    $newsData = [];
    foreach ($news as $item) {
        $newsData[] = [
            'id' => $item->id,
            'instagram_link' => $item->instagram,
            'telegram_link' => $item->telegram,
            'upi_id' => $item->upi_id,
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Settings listed successfully.',
        'data' => $newsData,
    ], 200);
}

public function appsettings_list(Request $request)
{
    // Retrieve all news settings
    $appsettings = Appsettings::all();

    if ($appsettings->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No Appsettings found.',
        ], 200);
    }

    $appsettingsData = [];
    foreach ($appsettings as $item) {
        $appsettingsData[] = [
            'id' => $item->id,
            'link' => $item->link,
            'app_version' => $item->app_version,
            'description' => $item->description,
            'login' => strval($item->login),
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'App Settings listed successfully.',
        'data' => $appsettingsData,
    ], 200);
}

public function privacy_policy(Request $request)
{
    // Retrieve all news settings
    $news = News::all();

    if ($news->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No privacy policy found.',
        ], 200);
    }

    $newsData = [];
    foreach ($news as $item) {
        $newsData[] = [
            'id' => $item->id,
            'privacy_policy' => $item->privacy_policy,
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Privacy Policy listed successfully.',
        'data' => $newsData,
    ], 200);
}

public function terms_conditions(Request $request)
{
    // Retrieve all news settings
    $news = News::all();

    if ($news->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No terms conditions found.',
        ], 200);
    }

    $newsData = [];
    foreach ($news as $item) {
        $newsData[] = [
            'id' => $item->id,
            'terms_conditions' => $item->terms_conditions,
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Terms Conditions listed successfully.',
        'data' => $newsData,
    ], 200);
}

public function refund_policy(Request $request)
{
    // Retrieve all news settings
    $news = News::all();

    if ($news->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No Refund policy found.',
        ], 200);
    }

    $newsData = [];
    foreach ($news as $item) {
        $newsData[] = [
            'id' => $item->id,
            'refund_policy' => $item->refund_policy,
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Refund Policy listed successfully.',
        'data' => $newsData,
    ], 200);
}

public function profile_view(Request $request)
{
    $user_id = $request->input('user_id');
    $profile_user_id = $request->input('profile_user_id'); // Renamed the variable to avoid conflict

    // Validate user_id and profile_user_id
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    if (empty($profile_user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'profile_user_id is empty.',
        ], 200);
    }

    // Check if user and profile_user exist
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    $profile_user = Users::find($profile_user_id);
    if (!$profile_user) {
        return response()->json([
            'success' => false,
            'message' => 'Profile user not found.',
        ], 200);
    }

       // Skip notification if user_id and profile_user_id are the same
       if ($user_id == $profile_user_id) {
        return response()->json([
            'success' => true,
            'message' => 'User viewed their own profile. No notification sent.',
        ], 200);
    }

    $existingNotification = Notifications::where('user_id', $profile_user_id)
        ->where('notify_user_id', $user_id)
        ->latest()
        ->first();

    if ($existingNotification) {
        $created_at = Carbon::parse($existingNotification->created_at);
        $now = Carbon::now();
        $diffInHours = $now->diffInHours($created_at);

        if ($diffInHours < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Notification already sent within the last hour.',
            ], 200);
        }
    }

    $currentTime = now();
    
    // Create and save the notification
    $notification = new Notifications();
    $notification->user_id = $profile_user_id;
    $notification->notify_user_id = $user_id;
    $notification->message = "{$user->name}, viewed your profile";
    $notification->datetime = now();


    if (!$notification->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save notification.',
        ], 500);
    }

     // Send notification to the profile user
     $this->sendNotificationToUser(strval($profile_user_id), "{$user->name} viewed your profile");

     return response()->json([
         'success' => true,
         'message' => 'Notification added successfully.',
     ], 201);
 }
protected function sendNotificationToUser($profile_user_id, $message)
{
    $this->oneSignalClient->sendNotificationToExternalUser(
        $message,
        $profile_user_id,
        $url = null,
        $data = null,
        $buttons = null,
        $schedule = null
    );
}
protected $oneSignalClient;

public function __construct(OneSignalClient $oneSignalClient)
{
    $this->oneSignalClient = $oneSignalClient;
}

public function send_notification(Request $request)
{

    $user_id = $request->input('user_id');
    $message = $request->input('message');
    $title = $request->input('title');

    // Validate inputs
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    if (empty($message)) {
        return response()->json([
            'success' => false,
            'message' => 'message is empty.',
        ], 200);
    }

    if (empty($title)) {
        return response()->json([
            'success' => false,
            'message' => 'title is empty.',
        ], 200);
    }
    
    // Attempt to send notification using your OneSignal client
    $response = $this->oneSignalClient->sendNotificationToExternalUser(
        "Some Message",
        $user_id,
        $url = null,
        $data = null,
        $buttons = null,
        $schedule = null
    );

    // Handle response from OneSignal
    if ($response) {
        // Notification successfully sent
        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully.',
        ], 201);
    } else {
        // Failed to send notification or $response is null
        return response()->json([
            'success' => false,
            'message' => 'Failed to send notification.', // You can customize this message
        ], 500);
    }
}


/*public function send_notification(Request $request)
{
    $user_id = $request->input('user_id');
    $message = $request->input('message');
    $title = $request->input('title');

    // Validate inputs
    if (empty($user_id) || empty($message) || empty($title)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id, message, or title is empty.',
        ], 200);
    }

    // Prepare the data for the request
    $data = [
        'app_id' => '4f929ed9-584d-4208-a3e8-7de1ae4f679e', // Replace with your OneSignal app_id
        'include_player_ids' => [$user_id], // Array of user_ids to send the notification to
        'contents' => ['en' => $message], // Message content
        'headings' => ['en' => $title], // Notification title
        // Add other parameters like URL, data, buttons, schedule as needed
    ];

    // Send the request using Guzzle HTTP client
    $client = new Client();
    try {
        $response = $client->request('POST', 'https://onesignal.com/api/v1/notifications', [
            'headers' => [
                'Authorization' => 'Basic ZGZhYWI3NzktNTEzYi00MDNkLWIzNGItZmU0YjAzZmZkZTI3', // Replace with your REST API key
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);

        // Handle response from OneSignal
        $statusCode = $response->getStatusCode();
        if ($statusCode === 200 || $statusCode === 201) {
            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully.',
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification.',
            ], 500);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}*/

    //  $response = $this->oneSignalClient->sendNotificationToExternalUser(
public function create_recharge(Request $request)
{
    // Validate required inputs
    if (empty($request->input('user_id'))) {
        return response()->json([
            'success' => false,
            'message' => 'User ID is empty',
        ]);
    }

    if (empty($request->input('txn_id'))) {
        return response()->json([
            'success' => false,
            'message' => 'Transaction ID is empty',
        ]);
    }

    if (empty($request->input('amount'))) {
        return response()->json([
            'success' => false,
            'message' => 'Amount is empty',
        ]);
    }

    if (empty($request->input('key'))) {
        return response()->json([
            'success' => false,
            'message' => 'Key is empty',
        ]);
    }

    // Prepare data for query
    $user_id = $request->input('user_id');
    $txn_id = $request->input('txn_id');
    $amount = $request->input('amount');
    $key = $request->input('key');
    $p_info = 'Recharge';

    // Fetch user information
    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Data Not found',
        ]);
    }

    $name = $user->name;
    $email = $user->email;
    $mobile = $user->mobile ? $user->mobile : '0000000000'; // Use default mobile if not available
    $redirect_url = 'https://www.google.com/';
    $datetime = now();

    // Validate mobile number
    if (!preg_match('/^\d{10}$/', $mobile)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid mobile number',
        ]);
    }

    // API endpoint
    $url = 'https://api.ekqr.in/api/create_order';

    // Data to be sent
    $data = [
        'client_txn_id' => $txn_id,
        'amount' => $amount,
        'p_info' => $p_info,
        'txn_date' => $datetime->toDateString(),
        'customer_name' => $name,
        'customer_email' => $email,
        'customer_mobile' => $mobile,
        'redirect_url' => $redirect_url,
        'key' => $key
    ];

    // Initialize HTTP client and send POST request
    $response = Http::post($url, $data);

    // Check for errors in the response
    if ($response->failed()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create order. Curl error: ' . $response->body(),
        ]);
    }

    $responseArray = $response->json(); 

    if (!$responseArray['status']) {
        return response()->json([
            'success' => false,
            'message' => 'Client_txn_id already Exits',
        ]);
    }

    $order_id = $responseArray['data']['order_id'];

    // Insert transaction into database
    try {
        $rechargeTrans = new RechargeTrans();
        $rechargeTrans->user_id = $user_id;
        $rechargeTrans->txn_id = $txn_id;
        $rechargeTrans->order_id = $order_id;
        $rechargeTrans->amount = $amount;
        $rechargeTrans->status = 0;
        $rechargeTrans->datetime = $datetime;
        $rechargeTrans->save();

        return response()->json(
            $responseArray,
        );
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save recharge transaction. Error: ' . $e->getMessage(),
        ]);
    }
}

public function check_recharge_status(Request $request)
{
    $input = $request->all();

    // Check required inputs
    if (empty($input['user_id'])) {
        return response()->json([
            'success' => false,
            'message' => 'User ID is empty',
        ]);
    }

    if (empty($input['txn_id'])) {
        return response()->json([
            'success' => false,
            'message' => 'Transaction ID is empty',
        ]);
    }

    if (empty($input['date'])) {
        return response()->json([
            'success' => false,
            'message' => 'Date is empty',
        ]);
    }

    if (empty($input['key'])) {
        return response()->json([
            'success' => false,
            'message' => 'Key is empty',
        ]);
    }

    if (empty($input['point_id'])) {
        return response()->json([
            'success' => false,
            'message' => 'Point ID is empty',
        ]);
    }

    // Escape inputs (if necessary, though not typically needed in Laravel ORM)
    $user_id = htmlspecialchars($input['user_id']);
    $txn_id = htmlspecialchars($input['txn_id']);
    $date = htmlspecialchars($input['date']);
    $key = htmlspecialchars($input['key']);
    $point_id = htmlspecialchars($input['point_id']);

    // API endpoint
    $url = 'https://api.ekqr.in/api/check_order_status';

    // Data to be sent
    $data = [
        'client_txn_id' => $txn_id,
        'txn_date' => $date,
        'key' => $key
    ];

    try {
         // Fetch points using point_id
         $pointEntry = Points::find($point_id);
         if (!$pointEntry) {
             return response()->json([
                 'success' => false,
                 'message' => 'Invalid Point ID',
             ]);
         }
         $points = $pointEntry->points;
 
         // Fetch user using user_id
         $user = Users::find($user_id);
         if (!$user) {
             return response()->json([
                 'success' => false,
                 'message' => 'Invalid user ID',
             ]);
         }
         $referred_by = $user->referred_by;

        // API endpoint
        $url = 'https://api.ekqr.in/api/check_order_status';

        // Data to be sent
        $data = [
            'client_txn_id' => $txn_id,
            'txn_date' => $date,
            'key' => $key
        ];

        // Initialize HTTP client and send POST request
        $response = Http::post($url, $data);

        // Check for errors in the response
        $responseArray = $response->json();
        $payment_Status = $responseArray['data']['status'];
        if($payment_Status != 'success'){
            return response()->json([
                'success' => false,
                'message' => 'Payment Failed',
            ]);
        }
        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check order status. API error: ' . json_encode($responseArray),
            ]);
        }

        // Find existing recharge transaction by txn_id
        $rechargeTrans = RechargeTrans::where('txn_id', $txn_id)->first();
        if (!$rechargeTrans) {
            return response()->json([
                'success' => false,
                'message' => 'Recharge transaction not found for txn_id: ' . $txn_id,
            ]);
        }

        // Check if user_id matches
        if ($rechargeTrans->user_id != $user_id) {
            return response()->json([
                'success' => false,
                'message' => 'User ID does not match the transaction ID',
            ]);
        }

                // Check current status before updating
            if ($rechargeTrans->status != 1) {
                // Update fields
                $rechargeTrans->txn_id = $data['client_txn_id'];
                $rechargeTrans->status = 1;
                $rechargeTrans->save();

                // Fetch user
                $user = Users::find($input['user_id']);
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found',
                    ]);
                }

                // Update user's points
                $user->points += $points;
                $user->total_points += $points;
                $user->save();

                // Insert into transactions table
                $transaction = new Transaction();
                $transaction->user_id = $input['user_id'];
                $transaction->points = $points;
                $transaction->datetime = now();
                $transaction->type = 'recharge';
                $transaction->save();

                // Find all users whose refer_code matches the referred_by value
                $referredUsers = Users::where('refer_code', $referred_by)->get();

                // Calculate 10% of points as bonus points
                $bonusPoints = $points * 0.10;

                foreach ($referredUsers as $referredUser) {
                    // Update each referred user's points
                    $referredUser->points += $bonusPoints;
                    $referredUser->total_points += $bonusPoints;
                    $referredUser->save();

                    // Insert into transactions table for each referred user
                    $refTransaction = new Transaction();
                    $refTransaction->user_id = $referredUser->id;
                    $refTransaction->points = $bonusPoints;
                    $refTransaction->datetime = now();
                    $refTransaction->type = 'bonus';
                    $refTransaction->save();
                }

                // Return success response
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction completed successfully',
                ]);
            } 
                } catch (\Exception $e) {
                    // Handle any exceptions
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to check order status. Error: ' . $e->getMessage(),
                    ]);
                }
}

public function create_verification(Request $request)
{
    // Validate required inputs
    if (empty($request->input('user_id'))) {
        return response()->json([
            'success' => false,
            'message' => 'User ID is empty',
        ]);
    }

    if (empty($request->input('txn_id'))) {
        return response()->json([
            'success' => false,
            'message' => 'Transaction ID is empty',
        ]);
    }

    if (empty($request->input('amount'))) {
        return response()->json([
            'success' => false,
            'message' => 'Amount is empty',
        ]);
    }

    if (empty($request->input('key'))) {
        return response()->json([
            'success' => false,
            'message' => 'Key is empty',
        ]);
    }

    // Prepare data for query
    $user_id = $request->input('user_id');
    $txn_id = $request->input('txn_id');
    $amount = $request->input('amount');
    $key = $request->input('key');
    $p_info = 'Recharge';

    // Fetch user information
    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User Not found',
        ]);
    }

    $name = $user->name;
    $email = $user->email;
    $mobile = $user->mobile ? $user->mobile : '0000000000'; // Use default mobile if not available
    $redirect_url = 'https://www.google.com/';
    $datetime = now();

    // Validate mobile number
    if (!preg_match('/^\d{10}$/', $mobile)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid mobile number',
        ]);
    }

    // API endpoint
    $url = 'https://api.ekqr.in/api/create_order';

    // Data to be sent
    $data = [
        'client_txn_id' => $txn_id,
        'amount' => $amount,
        'p_info' => $p_info,
        'txn_date' => $datetime->toDateString(),
        'customer_name' => $name,
        'customer_email' => $email,
        'customer_mobile' => $mobile,
        'redirect_url' => $redirect_url,
        'key' => $key
    ];

    // Initialize HTTP client and send POST request
    $response = Http::post($url, $data);

    // Check for errors in the response
    if ($response->failed()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create order. Curl error: ' . $response->body(),
        ]);
    }

    $responseArray = $response->json(); 

    if (!$responseArray['status']) {
        return response()->json([
            'success' => false,
            'message' => 'Client_txn_id already Exits',
        ]);
    }

    $order_id = $responseArray['data']['order_id'];

    // Insert transaction into database
    try {
        $verificationTrans = new VerificationTrans();
        $verificationTrans->user_id = $user_id;
        $verificationTrans->txn_id = $txn_id;
        $verificationTrans->order_id = $order_id;
        $verificationTrans->amount = $amount;
        $verificationTrans->status = 0;
        $verificationTrans->datetime = $datetime;
        $verificationTrans->save();

        return response()->json(
            $responseArray
        );
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save recharge transaction. Error: ' . $e->getMessage(),
        ]);
    }
}

public function verification_status(Request $request)
{
    // Emulate $_POST handling
    $input = $request->all();

    // Check required inputs
    if (empty($input['user_id'])) {
        return response()->json([
            'success' => false,
            'message' => 'User ID is empty',
        ]);
    }

    if (empty($input['txn_id'])) {
        return response()->json([
            'success' => false,
            'message' => 'Transaction ID is empty',
        ]);
    }

    if (empty($input['date'])) {
        return response()->json([
            'success' => false,
            'message' => 'Date is empty',
        ]);
    }

    if (empty($input['key'])) {
        return response()->json([
            'success' => false,
            'message' => 'Key is empty',
        ]);
    }

    if (empty($input['plan_id'])) {
        return response()->json([
            'success' => false,
            'message' => 'Plan ID is empty',
        ]);
    }

    // Escape inputs (if necessary, though not typically needed in Laravel ORM)
    $user_id = htmlspecialchars($input['user_id']);
    $txn_id = htmlspecialchars($input['txn_id']);
    $date = htmlspecialchars($input['date']);
    $key = htmlspecialchars($input['key']);
    $plan_id = htmlspecialchars($input['plan_id']);

    // API endpoint
    $url = 'https://api.ekqr.in/api/check_order_status';

    // Data to be sent
    $data = [
        'client_txn_id' => $txn_id,
        'txn_date' => $date,
        'key' => $key
    ];

    try {
        // Fetch plan information
        $planEntry = Plans::find($plan_id);
        if (!$planEntry) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Plan ID',
            ], 200);
        }

        $validity = $planEntry->validity;

        // Fetch user information
        $user = Users::find($user_id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid User ID',
            ], 200);
        }

        // Initialize HTTP client and send POST request
        $response = Http::post($url, $data);

        // Check for errors in the response
        $responseArray = $response->json();
        $payment_Status = $responseArray['data']['status'];
        if($payment_Status != 'success'){
            return response()->json([
                'success' => false,
                'message' => 'Payment Failed',
            ]);
        }
        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check order status. API error: ' . json_encode($responseArray),
            ]);
        }

        // Find existing verification transaction by txn_id
        $verificationTrans = VerificationTrans::where('txn_id', $txn_id)->first();
        if (!$verificationTrans) {
            return response()->json([
                'success' => false,
                'message' => 'Verification transaction not found for txn_id: ' . $txn_id,
            ], 200);
        }

        // Check if user_id matches
        if ($verificationTrans->user_id != $user_id) {
            return response()->json([
                'success' => false,
                'message' => 'User ID does not match the transaction ID',
            ], 200);
        }

        // Check current status before updating
        if ($verificationTrans->status != 1) {
            // Update fields
            $verificationTrans->status = 1;
            $verificationTrans->save();

              // Fetch user
              $user = Users::find($input['user_id']);
              if (!$user) {
                  return response()->json([
                      'success' => false,
                      'message' => 'User not found',
                  ]);
              }

               // Update payment_status in the verifications table
               $verification = Verifications::where('user_id', $user_id)

               ->first();

           if ($verification) {
            $verification->plan_id = $plan_id; // Set to 1 for paid
               $verification->payment_status = 1; // Set to 1 for paid
               $verification->save();
           }

            // Return success response  
            return response()->json([
                'success' => true,
                'message' => 'Verification completed successfully',
            ]);
        } else {
            // Return error response if status is already 1
            return response()->json([
                'success' => false,
                'message' => 'Verification already added',
            ], 200);
        }
    } catch (\Exception $e) {
        // Handle any exceptions
        Log::error('Error occurred while creating verification status', [
            'exception' => $e->getMessage(),
            'request_data' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to check order status. Error: ' . $e->getMessage(),
        ], 500);
    }
}
public function fakechat_list(Request $request)
{
    // Retrieve offset and limit from the request, with default values
    $offset = $request->input('offset', 0);
    $limit = $request->input('limit', 10); // Default limit to 10 if not provided

    // Count total records with status 1
    $totalCount = Fakechats::where('status', 1)->count();

    // Retrieve paginated fakechats with status 1 and include user details
    $fakechats = Fakechats::where('status', 1)
        ->skip($offset)
        ->take($limit)
        ->with('user') // Ensure the 'user' relationship is defined in the Fakechats model
        ->get();

    if ($fakechats->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No fakechat found.',
            'total' => $totalCount, // Include total count even if no data found
        ], 200);
    }

    $fakechatData = [];
    foreach ($fakechats as $fakechat) {
        $user = $fakechat->user; // Eager loaded user data

        // Calculate total unread messages for the user
        $unreadCount = Chats::where('user_id', $fakechat->user_id)
            ->sum('unread'); // Assuming 'unread' field holds the count of unread messages

        $fakechatData[] = [
            'id' => $fakechat->id,
            'user_id' => $fakechat->user_id,
            'name' => $user ? $user->name : "", // Include user name
            'profile' => $user ? asset('storage/app/public/users/' . $user->profile) : null, // Include user profile URL
            'status' => $fakechat->status,
            'unread_count' => $unreadCount, // Include unread count
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Fake Chats listed successfully.',
        'total' => $totalCount, // Include total count in the response
        'data' => $fakechatData,
    ], 200);
}


public function plan_list(Request $request)
{
    $plans = Plans::orderBy('price', 'desc')->get();

    if ($plans->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No plans found.',
        ], 200);
    }

    $plansDetails = [];

    foreach ($plans as $plan) {
        $plansDetails[] = [
            'id' => $plan->id,
            'plan_name' => $plan->plan_name,
            'validity' => $plan->validity,
            'price' => (string) $plan->price,
            'save_amount' => (string) $plan->save_amount,
            'updated_at' => Carbon::parse($plan->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($plan->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'plans Details retrieved successfully.',
        'data' => $plansDetails,
    ], 200);
}

public function corn_verify(Request $request)
{
    // Get current date
    $currentDate = now();

    // Get users with verification_end_value before the current date
    $users = Users::where('verification_end_date', '<', $currentDate)->get();

    // Check if there are users to update
    if ($users->isEmpty()) {
        return response()->json([
            'success' => true,
            'message' => 'No users found with verification_end_value before the current date.',
        ], 200);
    }

    // Update status to 0 for the selected users
    foreach ($users as $user) {
        $user->verified = 0;
        $user->save();
    }

    // Return a success message with the count of updated users
    return response()->json([
        'success' => true,
        'message' => 'Successfully updated',
    ], 200);
}

public function recharge_user_list(Request $request)
{
    $transactions = Transaction::where('type', 'recharge')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->with('user')
    ->get();

    if ($transactions->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No recharge transactions found.',
        ], 200);
    }

    $professionData = [];
    foreach ($transactions as $transaction) {
        $professionData[] = [
            'id' => $transaction->id,
            'user_id' => $transaction->user_id,
            'user_name' => $transaction->user ? $transaction->user->name : '',
            'city' => $transaction->user ? $transaction->user->city : '',
            'state' => $transaction->user ? $transaction->user->state : '',
            'type' => $transaction->type,
            'points' => $transaction->points,
            'datetime' => $transaction->datetime,
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Recharge transactions listed successfully.',
        'data' => $professionData,
    ], 200);
}

public function online_reset(Request $request)
{
    Users::query()->update(['online_status' => 0]);

    return response()->json([
        'success' => true,
        'message' => 'Online Status Reset successfully.',
    ], 200);
}


public function send_msg_all(Request $request)
{
    $user_id = $request->input('user_id');
    $message = $request->input('message');

    // Validate inputs
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    if (empty($message)) {
        return response()->json([
            'success' => false,
            'message' => 'Message is empty.',
        ], 200);
    }

    // Validate the sender user
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    // Fetch all users excluding the sender
    $allUsers = Users::where('id', '!=', $user_id)->get();

    if ($allUsers->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No other users found to send the message to.',
        ], 200);
    }

    $currentTime = now();

    // Begin a database transaction
    DB::beginTransaction();
    try {
        // Iterate over each user and create chat and notification entries
        foreach ($allUsers as $recipient) {
            // Check if the chat entry already exists for this pair
            $existingChat1 = Chats::where('user_id', $user_id)
                                   ->where('chat_user_id', $recipient->id)
                                   ->first();

            $existingChat2 = Chats::where('user_id', $recipient->id)
                                   ->where('chat_user_id', $user_id)
                                   ->first();

            // Create chat entry for the sender to the recipient if it doesn't exist
            if (!$existingChat1) {
                $newChat1 = new Chats();
                $newChat1->user_id = $user_id;
                $newChat1->chat_user_id = $recipient->id;
                $newChat1->latest_message = $message;
                $newChat1->unread = 0; // Assuming this user has seen the message
                $newChat1->msg_seen = 1; // Assuming this user has seen the message
                $newChat1->latest_msg_time = $currentTime;
                $newChat1->datetime = $currentTime;

                if (!$newChat1->save()) {
                    throw new \Exception('Failed to save Chat entry for user ID ' . $recipient->id);
                }
            }

            // Create chat entry for the recipient to the sender if it doesn't exist
            if (!$existingChat2) {
                $newChat2 = new Chats();
                $newChat2->user_id = $recipient->id;
                $newChat2->chat_user_id = $user_id;
                $newChat2->latest_message = $message;
                $newChat2->unread = 1; // Assuming this user has not seen the message yet
                $newChat2->msg_seen = 0; // Assuming this user has not seen the message yet
                $newChat2->latest_msg_time = $currentTime;
                $newChat2->datetime = $currentTime;

                if (!$newChat2->save()) {
                    throw new \Exception('Failed to save Chat entry for recipient ID ' . $recipient->id);
                }
            }

            // Create notification entry
            $notification = new Notifications();
            $notification->user_id = $recipient->id;
            $notification->notify_user_id = $user_id;
            $notification->message = "{$user->name} messaged you";
            $notification->datetime = $currentTime;

            if (!$notification->save()) {
                throw new \Exception('Failed to save Notification entry for recipient ID ' . $recipient->id);
            }

            // Save chat data to Firebase
            $this->saveChatToFirebase($user_id, $recipient->id, $message, $currentTime);

            $this->sendNotifiToallUser(strval($recipient->id), "{$user->name} messaged you");
        }

        // Commit the transaction
        DB::commit();

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Message sent to all users successfully.',
        ], 201);
    } catch (\Exception $e) {
        // Rollback the transaction if anything fails
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

protected function sendNotifiToallUser($recipientId, $message)
{
    // Check the online_status of the user
    $user = Users::find($recipientId); // Assuming User is your model class
    if ($user && $user->online_status == 0) {
        // User is offline, send notification via OneSignal
        try {
            $this->oneSignalClient->sendNotificationToExternalUser(
                $message,
                $recipientId,
                $url = null,
                $data = null,
                $buttons = null,
                $schedule = null
            );
        } catch (\Exception $e) {
            // Handle OneSignal notification error
            Log::error('Failed to send notification to user ID ' . $recipientId . ': ' . $e->getMessage());
        }
    }
}

private function saveChatToFirebase($userId, $recipientId, $message, $time)
{
    // Fetch user details
    $user = Users::find($userId);
    $recipientUser = Users::find($recipientId); // Fetch recipient by ID

    if (!$user || !$recipientUser) {
        // Handle the case where user or recipient details are not found
        return;
    }

    $userName = $user->name;
    $recipientName = $recipientUser->name;

    // Get unique names
    $userUniqueName = $user->unique_name; // Assuming the Users model has a `unique_name` attribute
    $recipientUniqueName = $recipientUser->unique_name; // Same assumption

    $firebase = (new \Kreait\Firebase\Factory())
        ->withServiceAccount(base_path('storage/app/firebase-auth.json'))
        ->withDatabaseUri('https://dudeways-c8f31-default-rtdb.asia-southeast1.firebasedatabase.app/')
        ->createDatabase();

    // Generate a random number between 100000 and 999999 for chatID
    $randomNumber = random_int(100000, 999999);

    // Prepare chat data with the required fields
    $chatData = [
        'attachmentType'=>"Text",
        'chatID' => $randomNumber,
        'dateTime' => $time->getTimestamp(), // Store as a Unix timestamp
        'message' => $message,
        'msgSeen' => false, // Assuming the message is not seen initially
        'receiverID' => $recipientId,
        'senderID' => $userId,
        'sentBy' => $userName,
        'type' => "Text",
        'typing' => false,
    ];

    // Use unique names to construct the path
    $path = 'CHATS_V2/' . $userUniqueName . '/' . $recipientUniqueName;

    // Save chat data to Firebase
    $firebase->getReference($path . '/' . $randomNumber)->set($chatData);
}

public function delete_profile(Request $request)
{
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    // Fetch the user from the database based on the provided user_id
    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    // Check if a profile image exists and delete it from storage
    if ($user->profile && Storage::exists('public/users/' . $user->profile)) {
        Storage::delete('public/users/' . $user->profile);
    }

    // Optionally: Clear the profile image path in the database if needed
    $user->profile = null;
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'User profile image deleted successfully.',
    ], 200);
}

public function send_msg_to_user(Request $request)
{
    $user_id = $request->input('user_id'); 
    $chat_user_id = $request->input('chat_user_id');
    $message = $request->input('message');

    // Validate inputs
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    if (empty($chat_user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'chat_user_id is empty.',
        ], 200);
    }

    if (empty($message)) {
        return response()->json([
            'success' => false,
            'message' => 'Message is empty.',
        ], 200);
    }

    // Check for self-chat
    if ($user_id == $chat_user_id) {
        return response()->json([
            'success' => false,
            'message' => 'You cannot chat with yourself.',
        ], 200);
    }

    // Validate users
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    $chat_user = Users::find($chat_user_id);
    if (!$chat_user) {
        return response()->json([
            'success' => false,
            'message' => 'Chat user not found.',
        ], 200);
    }

    $currentTime = now();

    // Check if a chat exists
    $existingChat = Chats::where(function($query) use ($user_id, $chat_user_id) {
        $query->where('user_id', $user_id)
              ->where('chat_user_id', $chat_user_id);
    })->orWhere(function($query) use ($user_id, $chat_user_id) {
        $query->where('user_id', $user_id)
              ->where('chat_user_id', $chat_user_id);
    })->first();

    try {
        if ($existingChat) {
            // Update the existing chat
            $existingChat->latest_message = $message;
            $existingChat->latest_msg_time = $currentTime;
            $existingChat->datetime = $currentTime;

            if (!$existingChat->save()) {
                throw new \Exception('Failed to update Chat.');
            }

            $responseMessage = 'Chat updated successfully.';
        } else {
            // Create new chat entries
            $newChat1 = new Chats();
            $newChat1->user_id = $user_id;
            $newChat1->chat_user_id = $chat_user_id;
            $newChat1->latest_message = $message;
            $newChat1->latest_msg_time = $currentTime;
            $newChat1->datetime = $currentTime;

            $newChat2 = new Chats();
            $newChat2->user_id = $chat_user_id;
            $newChat2->chat_user_id = $user_id;
            $newChat2->latest_message = $message;
            $newChat2->latest_msg_time = $currentTime;
            $newChat2->datetime = $currentTime;

            if (!$newChat1->save() || !$newChat2->save()) {
                throw new \Exception('Failed to save new Chat entries.');
            }

            $responseMessage = 'Chat added successfully.';
        }

        // Add notification entry
        $notification = new Notifications();
        $notification->user_id = $chat_user_id;
        $notification->notify_user_id = $user_id;
        $notification->message = "{$user->name} messaged you";
        $notification->datetime = $currentTime;
        if (!$notification->save()) {
            throw new \Exception('Failed to save notification.');
        }

        // Save chat data to Firebase
        $this->saveChatsToFirebase($user_id, $chat_user_id, $message, $currentTime);

        // Send notification to user
        $this->sendNotifiToParticularUser(strval($chat_user_id), "{$user->name} messaged you");

        return response()->json([
            'success' => true,
            'message' => $responseMessage,
        ], 201);

    } catch (\Exception $e) {
        Log::error('Chat operation failed', [
            'error' => $e->getMessage(),
            'user_id' => $user_id,
            'chat_user_id' => $chat_user_id,
            'message' => $message
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

protected function sendNotifiToParticularUser($chat_user_id, $message)
{
    // Check the online_status of the user
    $user = Users::find($chat_user_id); // Assuming User is your model class
    if ($user && $user->online_status == 0) {
        // User is offline, send notification via OneSignal
        try {
            $this->oneSignalClient->sendNotificationToExternalUser(
                $message,
                $chat_user_id,
                $url = null,
                $data = null,
                $buttons = null,
                $schedule = null
            );
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'error' => $e->getMessage(),
                'user_id' => $chat_user_id,
                'message' => $message
            ]);
        }
    }
}

private function saveChatsToFirebase($userId, $chatUserId, $message, $time)
{
    // Fetch user details
    $user = Users::find($userId);
    $chatUser = Users::find($chatUserId); // Fetch recipient by ID

    if (!$user || !$chatUser) {
        // Handle the case where user or recipient details are not found
        return;
    }

    $userName = $user->name;
    $recipientName = $chatUser->name;

    // Get unique names
    $userUniqueName = $user->unique_name; // Assuming the Users model has a `unique_name` attribute
    $recipientUniqueName = $chatUser->unique_name; // Same assumption

    try {
        $firebase = (new \Kreait\Firebase\Factory())
            ->withServiceAccount(base_path('storage/app/firebase-auth.json'))
            ->withDatabaseUri('https://dudeways-c8f31-default-rtdb.asia-southeast1.firebasedatabase.app/')
            ->createDatabase();

        // Generate a random number between 100000 and 999999 for chatID
        $randomNumber = random_int(100000, 999999);

        // Prepare chat data with the required fields
        $chatData = [
            'attachmentType' => "Text",
            'chatID' => $randomNumber,
            'dateTime' => $time->getTimestamp(), // Store as a Unix timestamp
            'message' => $message,
            'msgSeen' => false, // Assuming the message is not seen initially
            'receiverID' => $chatUserId,
            'senderID' => $userId,
            'sentBy' => $userName,
            'type' => "Text",
            'typing' => false,
        ];

        // Use unique names to construct the path
        $path = 'CHATS_V1/' . $userUniqueName . '/' . $recipientUniqueName;

        // Save chat data to Firebase
        $firebase->getReference($path . '/' . $randomNumber)->set($chatData);

    } catch (\Exception $e) {
        Log::error('Failed to save chat to Firebase', [
            'error' => $e->getMessage(),
            'user_id' => $userId,
            'chat_user_id' => $chatUserId,
            'message' => $message
        ]);
    }
}


public function active_users_list(Request $request)
{
    // Retrieve offset, limit, and user_id from the request, with default values
    $offset = $request->input('offset', 0); // Default offset is 0
    $limit = $request->input('limit', 10);  // Default limit is 10
    $excludeUserId = $request->input('user_id'); // The user_id to exclude if needed

    // Validate the user_id input
    if (empty($excludeUserId)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    // Check if the user_id exists in the Users table
    $user = Users::find($excludeUserId);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    // Get the current time and calculate the time one hour ago
    $currentDateTime = Carbon::now();
    $oneHourAgo = Carbon::now()->subHour();

    // Get the total count of users whose active_datetime is within the last one hour,
    // excluding the specific user if necessary
    $totalActiveUsersQuery = Users::whereBetween('active_datetime', [$oneHourAgo, $currentDateTime])
                                  ->where('id', '!=', $excludeUserId);
    $totalActiveUsers = $totalActiveUsersQuery->count();

    // Fetch active users whose active_datetime is within the last one hour,
    $activeUsers = $totalActiveUsersQuery->orderBy('active_datetime', 'desc') // Sort by active_datetime descending
                                        ->offset($offset)
                                        ->limit($limit)
                                        ->get();

    // Check if any active users are found
    if ($activeUsers->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No active users found.',
        ], 200);
    }

    // Map through the active users and prepare the data
    $activeUsersData = $activeUsers->map(function ($user) {
        // Image URLs
        $imageUrl = $user->profile ? asset('storage/app/public/users/' . $user->profile) : '';
        $coverimageUrl = $user->cover_img ? asset('storage/app/public/users/' . $user->cover_img) : '';

        return [
            'id' => $user->id,
            'name' => $user->name,
            'unique_name' => $user->unique_name,
            'email' => $user->email,
            'mobile' => $user->mobile ?? '',
            'gender' => $user->gender,
            'profile' => $imageUrl,
            'cover_img' => $coverimageUrl,
            'online_status' => $user->online_status, // Include the online status as needed
            'active_datetime' => Carbon::parse($user->active_datetime)->format('Y-m-d H:i:s'),
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Active Users details retrieved successfully.',
        'total' => $totalActiveUsers,
        'data' => $activeUsersData,
    ], 200);
}


public function users_list(Request $request)
{
    // Retrieve offset, limit, and user_id from the request, with default values
    $offset = $request->input('offset', 0); // Default offset is 0
    $limit = $request->input('limit', 10);  // Default limit is 10
    $excludeUserId = $request->input('user_id'); // The user_id to exclude if online_status is 1
    $gender = $request->input('gender'); // The gender to filter the users

    // Validate the user_id input
    if (empty($excludeUserId)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    // Check if the user_id exists in the Users table
    $user = Users::find($excludeUserId);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    // Get the total count of users, excluding the specific user if necessary
    $totalUsersQuery = Users::where('id', '!=', $excludeUserId);

    // Apply gender filter if provided
    if (!empty($gender) && $gender !== 'all') {
        $totalUsersQuery->where('gender', $gender);
    }

    $totalUsers = $totalUsersQuery->count();

    // Fetch users, excluding the specific user if necessary,
    // applying offset, limit, and ordering by datetime
    $usersQuery = $totalUsersQuery->orderBy('datetime', 'desc')
                                 ->offset($offset)
                                 ->limit($limit);

    $users = $usersQuery->get();

    // Check if any users are found
    if ($users->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No users found.',
        ], 200);
    }

    // Map through the users and prepare the data
    $usersData = $users->map(function ($user) {
        // Image URLs
        $imageUrl = $user->profile ? asset('storage/app/public/users/' . $user->profile) : '';
        $coverimageUrl = $user->cover_img ? asset('storage/app/public/users/' . $user->cover_img) : '';

        return [
            'id' => $user->id,
            'name' => $user->name,
            'unique_name' => $user->unique_name,
            'verified' => $user->verified,
            'introduction' => $user->introduction,
            'age' => $user->age,
            'email' => $user->email,
            'mobile' => $user->mobile ?? '',
            'gender' => $user->gender,
            'profile' => $imageUrl,
            'cover_img' => $coverimageUrl,
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'User details retrieved successfully.',
        'total' => $totalUsers,
        'data' => $usersData,
    ], 200);
}
  public function msg_seen(Request $request)
    {
        $user_id = $request->input('user_id');
        $chat_user_id = $request->input('chat_user_id');

        // Validate user_id
        if (empty($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'user_id is empty.',
            ], 200);
        }

        // Validate chat_user_id
        if (empty($chat_user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'chat_user_id is empty.',
            ], 200);
        }

        // Find all chats where the user_id and chat_user_id match
        $chats = Chats::where(function($query) use ($user_id, $chat_user_id) {
            $query->where('user_id', $user_id)
                ->where('chat_user_id', $chat_user_id);
        })->get();

        if ($chats->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No chats found.',
            ], 200);
        }

        return response()->json([
            'success' => true,
            'chats' => $chats,
        ], 200);
    }

    public function unread_all(Request $request)
    {
        $user_id = $request->input('user_id');

        // Validate user_id
        if (empty($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'user_id is empty.',
            ], 200);
        }

        // Find all chats where the user_id matches
        $chats = Chats::where('user_id', $user_id)->get();

        if ($chats->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No chats found.',
            ], 200);
        }
        $unreadMessagesSum = Chats::where('user_id', $user_id)
        ->where('unread', '>', 0)  // Assuming 'unread' is a numeric field
        ->sum('unread');

        // Update the unread value to 0 for all chats
        $chats->each(function ($chat) {
            $chat->unread = 0;
            $chat->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Unread all successfully.',
            'unread_count' => strval($unreadMessagesSum),
        ], 200);
    }

    public function auto_view_profile(Request $request)
    {
        // Get the current time (hour and minute only)
        $currentHourMinute = now()->format('Y-m-d H:i');
    
        // Fetch the records from auto_view_profile where view_datetime matches the current hour and minute
        $autoViewProfiles = AutoViewProfile::whereRaw("DATE_FORMAT(view_datetime, '%Y-%m-%d %H:%i') = ?", [$currentHourMinute])->get();
    
        // Iterate through each record and send notifications
        foreach ($autoViewProfiles as $autoViewProfile) {
            $user_id = $autoViewProfile->user_id; // The user who is viewing the profile
            $view_user_id = $autoViewProfile->view_user_id; // The user whose profile is being viewed
    
            // Fetch the user who is viewing the profile
            $user = Users::find($user_id);
            if (!$user) {
                continue; // Skip if the user doesn't exist
            }
    
            // Fetch the user whose profile is being viewed
            $view_user = Users::find($view_user_id);
            if (!$view_user) {
                continue; // Skip if the viewed user doesn't exist
            }
    
            // Create and save the notification
            $notification = new Notifications();
            $notification->user_id = $user_id;
            $notification->notify_user_id = $view_user_id;
            $notification->message = "{$view_user->name}, viewed your profile";
            $notification->datetime = now();
    
            if (!$notification->save()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save notification for some profiles.',
                ], 500);
            }
    
            // Send notification to the profile user
            $this->sendNotificationToprofileUser(strval($user_id), "{$view_user->name} viewed your profile");
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Notifications sent successfully.',
        ], 201);
    }
    
    protected function sendNotificationToprofileUser($user_id, $message)
    {
        $this->oneSignalClient->sendNotificationToExternalUser(
            $message,
            $user_id,
            $url = null,
            $data = null,
            $buttons = null,
            $schedule = null
        );
    }
    
    public function delete_account(Request $request)
    {
        $user_id = $request->input('user_id');

        if (empty($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'user_id is empty.',
            ], 200);
        }

        $user = Users::find($user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 200);
        }

        // Delete the user from the database
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ], 200);
    }

    public function update_bank(Request $request)
{
    $user_id = $request->input('user_id');

    // Check if user_id is provided
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    // Check if the user exists in the Users table
    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found in Users table.',
        ], 200);
    }

    // Manually check each field and return custom messages if they are missing
    if (empty($request->input('account_holder_name'))) {
        return response()->json([
            'success' => false,
            'message' => 'Account holder name is empty.',
        ], 200);
    }

    if (empty($request->input('account_number'))) {
        return response()->json([
            'success' => false,
            'message' => 'Account number is empty.',
        ], 200);
    }

    if (empty($request->input('ifsc_code'))) {
        return response()->json([
            'success' => false,
            'message' => 'IFSC code is empty.',
        ], 200);
    }

    if (empty($request->input('bank_name'))) {
        return response()->json([
            'success' => false,
            'message' => 'Bank name is empty.',
        ], 200);
    }

    if (empty($request->input('branch_name'))) {
        return response()->json([
            'success' => false,
            'message' => 'Branch name is empty.',
        ], 200);
    }

    // Prepare the data for updating or inserting
    $data = [
        'account_holder_name' => $request->input('account_holder_name'),
        'account_number' => $request->input('account_number'),
        'ifsc_code' => $request->input('ifsc_code'),
        'bank_name' => $request->input('bank_name'),
        'branch_name' => $request->input('branch_name'),
    ];

    // Check if the user exists in the BankDetails table
    $bankDetail = BankDetails::where('user_id', $user_id)->first();

    if ($bankDetail) {
        // If user exists in BankDetails, update their bank information
        $bankDetail->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Bank details updated successfully.',
        ], 200);
    } else {
        // If user doesn't exist in BankDetails, insert a new record
        $data['user_id'] = $user_id;
        BankDetails::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Bank details added successfully.',
        ], 200);
    }
}
public function withdrawals(Request $request)
{
    $user_id = $request->input('user_id');
    $amount = $request->input('amount');

    // Check if user_id is provided
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    // Check if the amount is provided
    if (empty($amount)) {
        return response()->json([
            'success' => false,
            'message' => 'Amount is empty.',
        ], 200);
    }

    // Check if the user exists in the Users table
    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found in Users table.',
        ], 200);
    }

    // Get the user's balance from the Users table
    $user_balance = $user->balance;

    // Check if the amount is greater than the minimum withdrawal limit (50)
    if ($amount < 50) {
        return response()->json([
            'success' => false,
            'message' => 'Minimum withdrawal amount should be 50.',
        ], 200);
    }

    // Check if the amount exceeds the user's available balance
    if ($amount > $user_balance) {
        return response()->json([
            'success' => false,
            'message' => 'Insufficient balance.',
        ], 200);
    }

    // Check if the user has bank details
    $bankDetail = BankDetails::where('user_id', $user_id)->first();

    if (!$bankDetail) {
        return response()->json([
            'success' => false,
            'message' => 'Please update your bank details before making a withdrawal.',
        ], 200);
    }

    $pendingWithdrawal = Withdrawals::where('user_id', $user_id)
                                     ->where('status', 0) 
                                     ->first();

    if ($pendingWithdrawal) {
        return response()->json([
            'success' => false,
            'message' => 'Please wait, your existing withdrawal is pending.',
        ], 200);
    }

    // Deduct the withdrawal amount from the Users table balance
    $user->balance -= $amount;
    $user->save(); // Save the updated user balance

    // Check if the user exists in the Wallets table
    $wallet = Wallets::where('user_id', $user_id)->first();

    if ($wallet) {
        // If user exists in Wallets, update their balance
        $wallet->balance = $user->balance; // Update with the new balance from Users
        $wallet->save();
    } else {
        // If user doesn't exist in Wallets, create a new record
        Wallets::create([
            'user_id' => $user_id,
            'balance' => $user->balance, // Set balance to the current balance from Users
        ]);
    }

    // Insert the withdrawal into the Withdrawals table with specific withdrawal_date and status = 0
    Withdrawals::create([
        'user_id' => $user_id,
        'amount' => $amount,
        'datetime' => now(), // Set the current datetime
        'status' => 0, // Set status to 0
    ]);

    // Return response with the updated user balance
    return response()->json([
        'success' => true,
        'message' => 'Withdrawal successful.',
        'balance' => $user->balance,
    ], 200);
}



public function withdrawals_list(Request $request)
{
    // Retrieve user_id from request
    $user_id = $request->input('user_id');


    // Retrieve all withdrawals for the given user_id
    $withdrawals = Withdrawals::where('user_id', $user_id)->get();

    // Check if any withdrawals exist for this user
    if ($withdrawals->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No withdrawals found for this user.',
        ], 200);
    }

    // Prepare the withdrawal data
    $withdrawalsData = [];
    foreach ($withdrawals as $withdrawal) {
        $withdrawalsData[] = [
            'id' => $withdrawal->id,
            'user_id' => $withdrawal->user_id,
            'amount' => $withdrawal->amount,
            'status' => $withdrawal->status,
            'datetime' => $withdrawal->datetime, // Assuming this field exists
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Withdrawals listed successfully.',
        'data' => $withdrawalsData,
    ], 200);
}


public function add_reports(Request $request)
{
    $user_id = $request->input('user_id'); 
    $chat_user_id = $request->input('chat_user_id'); 
    $message = $request->input('message'); // Renamed the variable to avoid conflict

    // Validate user_id and feedbackContent
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    if (empty($chat_user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'chat_user_id is empty.',
        ], 200);
    }

    if (empty($message)) {
        return response()->json([
            'success' => false,
            'message' => 'message is empty.',
        ], 200);
    }

    // Check if user exists
    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 200);
    }
       // Check if user exists
       $chat_user = Users::find($chat_user_id);
       if (!$chat_user) {
           return response()->json([
               'success' => false,
               'message' => 'chat user not found.',
           ], 200);
       }

    // Create a new Feedback instance
    $report = new Reports();
    $report->user_id = $user_id; 
    $report->chat_user_id = $chat_user_id; 
    $report->message = $message; // Assign the feedback content to the model property

    // Save the feedback
    if (!$report->save()) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to save report.',
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'Reports added successfully.',
    ], 201);
}

public function user_earnings(Request $request)
{
    $user_id = $request->input('user_id');
    $type = $request->input('type');

    // Check if user_id is provided
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    if (empty($type)) {
        return response()->json([
            'success' => false,
            'message' => 'type is empty.',
        ], 200);
    }

    // Find the user by user_id
    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 200);
    }

        // Check if type is passed and if it's 'with_verification'
        if ($type === 'with_verification') {
            $selfi_image = $request->file('selfi_image');
            $proof_image = $request->file('proof_image');
        
            if (empty($selfi_image)) {
                return response()->json([
                    'success' => false,
                    'message' => 'selfi_image is empty.',
                ], 200);
            }
        
            if (empty($proof_image)) {
                return response()->json([
                    'success' => false,
                    'message' => 'proof_image is empty.',
                ], 200);
            }

            // Save the selfie image
            $selfieImagePath = $selfi_image->store('users', 'public');
            $user->selfi_image = basename($selfieImagePath);

            // Save the proof image
            $proofImagePath = $proof_image->store('users', 'public');
            $user->proof_image = basename($proofImagePath);

            $user->save();

            // Image URLs
            $selfieImageUrl = asset('storage/app/public/users/' . $user->selfi_image);
            $proofImageUrl = asset('storage/app/public/users/' . $user->proof_image);

            return response()->json([
                'success' => true,
                'message' => 'Under Verification.',
                'selfi_image_url' => $selfieImageUrl,
                'proof_image_url' => $proofImageUrl
            ], 200);
        }

    // If the type is 'without_verification'
    else if ($type === 'without_verification') {
        return response()->json([
            'success' => true,
            'message' => 'User profile without verification.',
        ], 200);
    }

    // If type is not valid
    return response()->json([
        'success' => false,
        'message' => 'Invalid type provided.',
    ], 400);
}

public function update_mobile(Request $request)
{
    $user_id = $request->input('user_id');

    // Check if user_id is provided
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    // Find the user by ID
    $user = Users::find($user_id);

    // Check if user exists
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 200);
    }

    // Get the mobile number from the request
    $mobile = $request->input('mobile');

    // Check if mobile number is provided
    if (is_null($mobile)) {
        return response()->json([
            'success' => false,
            'message' => 'mobile is empty.',
        ], 200);
    }

    // Validate the mobile number
    if (!preg_match('/^\d{10}$/', $mobile)) {
        return response()->json([
            'success' => false,
            'message' => 'Mobile number must be exactly 10 digits.',
        ], 200);
    }

    // Update user's mobile number
    $user->mobile = $mobile;

    // Save the updated user details
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'Mobile updated successfully.',
    ], 200);
}

public function selfi_image(Request $request)
{
    $user_id = $request->input('user_id');
    $type = $request->input('type', 'with_verification'); // Set default to 'with_verification'

    // Check if user_id is provided
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    // No need to check if $type is empty since it defaults to 'with_verification'
    
    // Find the user by user_id
    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 200);
    }

    // Now, you can assume $type will be 'with_verification' or whatever value is passed
    if ($type === 'with_verification') {
        $selfi_image = $request->file('selfi_image');

        if (empty($selfi_image)) {
            return response()->json([
                'success' => false,
                'message' => 'selfi_image is empty.',
            ], 200);
        }

        // Save the selfie image
        $selfieImagePath = $selfi_image->store('users', 'public');
        $user->selfi_image = basename($selfieImagePath);

        $user->save();

        // Image URLs
        $selfieImageUrl = asset('storage/app/public/users/' . $user->selfi_image);

        return response()->json([
            'success' => true,
            'message' => 'Selfie Image Verified successfully.',
            'selfi_image_url' => $selfieImageUrl
        ], 200);
    }
}


public function proof_image(Request $request)
{
    $user_id = $request->input('user_id');
    $type = $request->input('type', 'with_verification'); // Set default to 'with_verification'

    // Check if user_id is provided
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    // No need to check if $type is empty since it defaults to 'with_verification'
    
    // Find the user by user_id
    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 200);
    }

    // Now, you can assume $type will be 'with_verification' or whatever value is passed
    if ($type === 'with_verification') {
        $proof_image = $request->file('proof_image');

        if (empty($proof_image)) {
            return response()->json([
                'success' => false,
                'message' => 'proof_image is empty.',
            ], 200);
        }

        // Save the proof image
        $proofImagePath = $proof_image->store('users', 'public');
        $user->proof_image = basename($proofImagePath);

        $user->save();

        $proofImageUrl = asset('storage/app/public/users/' . $user->proof_image);

        return response()->json([
            'success' => true,
            'message' => 'Proof Image Verified successfully.',
            'proof_image_url' => $proofImageUrl
        ], 200);
    }
}

}
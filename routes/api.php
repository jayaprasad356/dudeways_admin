<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('check_mobile', [AuthController::class, 'check_mobile']);
Route::post('check_email', [AuthController::class, 'check_email']);
Route::post('register', [AuthController::class, 'register']);
Route::post('userdetails', [AuthController::class, 'userdetails']);
Route::post('other_userdetails', [AuthController::class, 'other_userdetails']);
Route::post('update_image', [AuthController::class, 'update_image']);
Route::post('update_cover_img', [AuthController::class, 'update_cover_img']);
Route::post('update_users', [AuthController::class, 'update_users']);
Route::post('update_notify', [AuthController::class, 'update_notify']);
Route::post('add_trip', [AuthController::class, 'add_trip']);
Route::post('update_trip_image', [AuthController::class, 'update_trip_image']);
Route::post('update_trip', [AuthController::class, 'update_trip']);
Route::post('trip_list', [AuthController::class, 'trip_list']);
Route::post('my_trip_list', [AuthController::class, 'my_trip_list']);
Route::post('trip_date', [AuthController::class, 'trip_date']);
Route::post('latest_trip', [AuthController::class, 'latest_trip']);
Route::post('recommend_trip_list', [AuthController::class, 'recommend_trip_list']);
Route::post('delete_trip', [AuthController::class, 'delete_trip']);
Route::post('add_chat', [AuthController::class, 'add_chat']);
Route::post('chat_list', [AuthController::class, 'chat_list']);
Route::post('blocked_chat', [AuthController::class, 'blocked_chat']);
Route::post('delete_chat', [AuthController::class, 'delete_chat']);
Route::post('add_friends', [AuthController::class, 'add_friends']);
Route::post('friends_list', [AuthController::class, 'friends_list']);
Route::post('add_notifications', [AuthController::class, 'add_notifications']);
Route::post('notification_list', [AuthController::class, 'notification_list']);
Route::post('verifications', [AuthController::class, 'verifications']);
Route::post('verify_front_image', [AuthController::class, 'verify_front_image']);
Route::post('verify_back_image', [AuthController::class, 'verify_back_image']);
Route::post('verify_selfie_image', [AuthController::class, 'verify_selfie_image']);
Route::post('points_list', [AuthController::class, 'points_list']);
Route::post('add_points', [AuthController::class, 'add_points']);
Route::post('reward_points', [AuthController::class, 'reward_points']);
Route::post('spin_points', [AuthController::class, 'spin_points']);
Route::post('add_feedback', [AuthController::class, 'add_feedback']);
Route::post('update_location', [AuthController::class, 'update_location']);
Route::post('profession_list', [AuthController::class, 'profession_list']);
Route::post('settings_list', [AuthController::class, 'settings_list']);
Route::post('profile_view', [AuthController::class, 'profile_view']);
Route::post('send_notification', [AuthController::class, 'send_notification']);
Route::post('create_recharge', [AuthController::class, 'create_recharge']);
Route::post('check_recharge_status', [AuthController::class, 'check_recharge_status']);
Route::post('privacy_policy', [AuthController::class, 'privacy_policy']);
Route::post('terms_conditions', [AuthController::class, 'terms_conditions']);


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

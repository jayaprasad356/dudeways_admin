<?php

namespace App\Http\Controllers;

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
Route::post('recent_trip', [AuthController::class, 'recent_trip']);
Route::post('recommend_trip_list', [AuthController::class, 'recommend_trip_list']);
Route::post('delete_trip', [AuthController::class, 'delete_trip']);
Route::post('add_chat', [AuthController::class, 'add_chat']);
Route::post('chat_list', [AuthController::class, 'chat_list']);
Route::post('blocked_chat', [AuthController::class, 'blocked_chat']);
Route::post('delete_chat', [AuthController::class, 'delete_chat']);
Route::post('read_chats', [AuthController::class, 'read_chats']);
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
Route::post('create_verification', [AuthController::class, 'create_verification']);
Route::post('verification_status', [AuthController::class, 'verification_status']);
Route::post('privacy_policy', [AuthController::class, 'privacy_policy']);
Route::post('terms_conditions', [AuthController::class, 'terms_conditions']);
Route::post('refund_policy', [AuthController::class, 'refund_policy']);
Route::post('appsettings_list', [AuthController::class, 'appsettings_list']);
Route::post('fakechat_list', [AuthController::class, 'fakechat_list']);
Route::post('plan_list', [AuthController::class, 'plan_list']);
Route::post('verification_list', [AuthController::class, 'verification_list']);
Route::post('corn_verify', [AuthController::class, 'corn_verify']);
Route::post('recharge_user_list', [AuthController::class, 'recharge_user_list']);
Route::post('send_msg_all', [AuthController::class, 'send_msg_all']);
Route::post('delete_profile', [AuthController::class, 'delete_profile']);
Route::post('send_msg_to_user', [AuthController::class, 'send_msg_to_user']);
Route::post('active_users_list', [AuthController::class, 'active_users_list']);
Route::post('users_list', [AuthController::class, 'users_list']);
Route::post('payment_image', [AuthController::class, 'payment_image']);
Route::get('online_reset', [AuthController::class, 'online_reset']);
Route::post('msg_seen', [AuthController::class, 'msg_seen']);
Route::post('unread_all', [AuthController::class, 'unread_all']);


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

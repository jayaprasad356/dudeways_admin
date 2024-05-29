<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('check_mobile', [AuthController::class, 'check_mobile']);
Route::post('register', [AuthController::class, 'register']);
Route::post('userdetails', [AuthController::class, 'userdetails']);
Route::post('update_image', [AuthController::class, 'update_image']);
Route::post('update_cover_img', [AuthController::class, 'update_cover_img']);
Route::post('update_users', [AuthController::class, 'update_users']);
Route::post('add_trip', [AuthController::class, 'add_trip']);
Route::post('update_trip', [AuthController::class, 'update_trip']);
Route::post('trip_list', [AuthController::class, 'trip_list']);
Route::post('my_trip_list', [AuthController::class, 'my_trip_list']);
Route::post('trip_date', [AuthController::class, 'trip_date']);
Route::post('latest_trip', [AuthController::class, 'latest_trip']);
Route::post('delete_trip', [AuthController::class, 'delete_trip']);
Route::post('chat_list', [AuthController::class, 'chat_list']);
Route::post('add_friends', [AuthController::class, 'add_friends']);
Route::post('friends_list', [AuthController::class, 'friends_list']);
Route::post('add_notifications', [AuthController::class, 'add_notifications']);
Route::post('notification_list', [AuthController::class, 'notification_list']);
Route::post('verifications', [AuthController::class, 'verifications']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

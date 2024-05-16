<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('check_mobile', [AuthController::class, 'check_mobile']);
Route::post('register', [AuthController::class, 'register']);
Route::post('userdetails', [AuthController::class, 'userdetails']);
Route::post('update_image', [AuthController::class, 'update_image']);
Route::post('update_users', [AuthController::class, 'update_users']);
Route::post('plan_trip', [AuthController::class, 'plan_trip']);
Route::post('update_trip', [AuthController::class, 'update_trip']);
Route::post('trip_list', [AuthController::class, 'trip_list']);
Route::post('/all-shop-details', [AuthController::class, 'allshopdetails']);
Route::post('/add-offer', [AuthController::class, 'addoffers']);
Route::post('/edit-offer', [AuthController::class, 'editoffers']);
Route::post('/delete-offer', [AuthController::class, 'deleteoffers']);
Route::post('/offer-details', [AuthController::class, 'offerdetails']);
Route::post('/offer-locked', [AuthController::class, 'offerlocked']);
Route::post('/slide-list', [AuthController::class, 'slide']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

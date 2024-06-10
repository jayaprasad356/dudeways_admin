<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\TripsController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;    
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FriendsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\VerificationsController;
use App\Http\Controllers\BulkUserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('/admin');
});

Auth::routes();



Route::namespace('Auth')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/register', 'RegisterController@showRegistrationForm')->name('register');
    Route::post('/register', 'RegisterController@register');

    Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('/password/reset', 'ResetPasswordController@reset');
});
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    Route::resource('customers', CustomerController::class);


    //User
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
    Route::get('/users/{users}/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::delete('/users/{users}', [UsersController::class, 'destroy'])->name('users.destroy');
    Route::put('/users/{users}', [UsersController::class, 'update'])->name('users.update');
    Route::post('/users', [UsersController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/add-points', [UsersController::class, 'addPointsForm'])->name('users.add_points');
    Route::post('/users/{id}/add-points', [UsersController::class, 'addPoints'])->name('users.store_points');


   
     //Trips  
     Route::get('/trips', [TripsController::class, 'index'])->name('trips.index');
     Route::get('/trips/create', [TripsController::class, 'create'])->name('trips.create');
     Route::get('/trips/{trips}/edit', [TripsController::class, 'edit'])->name('trips.edit');
     Route::delete('/trips/{trips}', [TripsController::class, 'destroy'])->name('trips.destroy');
     Route::put('/trips/{trips}', [TripsController::class, 'update'])->name('trips.update');
     Route::post('/trips', [TripsController::class, 'store'])->name('trips.store');


     //Chats  
     Route::get('/chats', [ChatsController::class, 'index'])->name('chats.index');
     Route::get('/chats/create', [ChatsController::class, 'create'])->name('chats.create');
     Route::get('/chats/{chats}/edit', [ChatsController::class, 'edit'])->name('chats.edit');
     Route::delete('/chats/{chats}', [ChatsController::class, 'destroy'])->name('chats.destroy');
     Route::put('/chats/{chats}', [ChatsController::class, 'update'])->name('chats.update');
     Route::post('/chats', [ChatsController::class, 'store'])->name('chats.store');

      //Points  
      Route::get('/points', [PointsController::class, 'index'])->name('points.index');
      Route::get('/points/create', [PointsController::class, 'create'])->name('points.create');
      Route::get('/points/{points}/edit', [PointsController::class, 'edit'])->name('points.edit');
      Route::delete('/points/{points}', [PointsController::class, 'destroy'])->name('points.destroy');
      Route::put('/points/{points}', [PointsController::class, 'update'])->name('points.update');
      Route::post('/points', [PointsController::class, 'store'])->name('points.store');

       //friends  
       Route::get('/friends', [FriendsController::class, 'index'])->name('friends.index');
       Route::delete('/friends/{friends}', [FriendsController::class, 'destroy'])->name('friends.destroy');


        //Notifications  
        Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
        Route::delete('/notifications/{notifications}', [NotificationsController::class, 'destroy'])->name('notifications.destroy');
      
        Route::get('/news/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('/news', [NewsController::class, 'update'])->name('news.update');

    
        //Verifications  
        Route::get('/verifications', [VerificationsController::class, 'index'])->name('verifications.index');
        Route::delete('/verifications/{verifications}', [VerificationsController::class, 'destroy'])->name('verifications.destroy');
 
        //Bulk Users
       // web.php
// Define the route for the "Upload Bulk Users" page
Route::get('bulk-users/upload', [BulkUserController::class, 'create'])->name('bulk-users.upload');
Route::post('bulk-users/upload', [BulkUserController::class, 'store'])->name('bulk-users.store');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/change-qty', [CartController::class, 'changeQty']);
    Route::delete('/cart/delete', [CartController::class, 'delete']);
    Route::delete('/cart/empty', [CartController::class, 'empty']);
});

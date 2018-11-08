<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register'); 
Route::get('user/{id}', 'API\UserController@show');

/**
 * Route with middleware
 * Untuk mengakses route di bawah ini
 * memerlukan Authorization Bearer Token login
 */
Route::group(['middleware' => 'auth:api'], function(){
    // -- Private Chat route -- //
    Route::apiResource('chat/private','API\PrivateChatController')->only(['store','index','destroy']);
    
    // -- Group Chat route -- //
    Route::apiResource('chat/group','API\GroupChatController')->except('update');
    
	// --- Group route --- //
    Route::apiResource('group','API\GroupController');
    
    // --- Profile Route --- //
	Route::get('profile', 'API\ProfileController@myProfile'); // show my profile
	Route::put('profile/update', 'API\ProfileController@updateProfile'); // change profile
	Route::post('profile/update-photo', 'API\ProfileController@updatePhoto'); //update photo profile
});

/**
 * Route dibawah ini cuma buat test aja
 */
Route::get('users', 'API\UserController@show'); // menampilkan semua user yang ada di database
Route::get('groups', 'API\GroupController@showAll'); // menampilkan semua group yang ada di database
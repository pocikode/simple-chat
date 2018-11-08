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
	Route::group(['prefix' => 'chat'], function(){
		// -- Private Chat route -- //
		// Route::post('private/send', 'API\PrivateChatController@send'); // send chat to other user
		// Route::post('private/show', 'API\PrivateChatController@show'); // show all chat by user
        // Route::delete('private/delete', 'API\PrivateChatController@delete'); // delete chat
        Route::apiResource('private','API\PrivateChatController')->only(['store','index','destroy']);
        
        // -- Group Chat route -- //
		Route::post('group/show/{group_id?}', 'API\GroupChatController@show'); // show group chat
		Route::post('group/send', 'API\GroupChatController@send'); // send group chat
		Route::post('group/delete', 'API\GroupChatController@delete'); // send group chat
    });
    
	// --- Group route --- //
	Route::post('group/create', 'API\GroupController@create'); // create new group
	Route::post('group/add-user', 'API\GroupController@addUser'); //add another user to group
	Route::delete('group/exit', 'API\GroupController@exit'); // exit group
	Route::get('group/show/{id?}', 'API\GroupController@show'); // tampilkan group user
    
    // --- Profile Route --- //
	Route::get('profile', 'API\ProfileController@myProfile'); // show my profile
	Route::put('profile/update', 'API\ProfileController@updateProfile'); // change profile
	Route::post('profile/update-photo', 'API\ProfileController@updatePhoto'); //update photo profile
});

/**
 * Route dibawah ini cuma buat test aja
 */
Route::get('user', 'API\UserController@show'); // menampilkan semua user yang ada di database
Route::get('group', 'API\GroupController@showAll'); // menampilkan semua group yang ada di database
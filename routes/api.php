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
Route::get('profile/{id?}', 'API\ProfileController@show'); // show profile by id
Route::get('user/{id?}', 'API\UserController@show');

// Passport route
Route::group(['middleware' => 'auth:api'], function(){
	Route::get('details', 'API\UserController@details');
	Route::group(['prefix' => 'chat'], function(){
		// private chat route
		Route::post('private/send', 'API\PrivateChatController@send'); // send chat to other user
		Route::post('private/show', 'API\PrivateChatController@show'); // show all chat by user
		Route::post('private/delete', 'API\PrivateChatController@delete'); // delete chat
		// group chat route
		Route::post('group/show', 'API\GroupChatController@show'); // show group chat
		Route::post('group/send', 'API\GroupChatController@send'); // send group chat
	});
	// group route
	Route::post('group/create', 'API\GroupController@create'); // create new group
	Route::post('group/add-user', 'API\GroupController@addUser'); //add another user to group
	Route::delete('group/exit', 'API\GroupController@exit'); // exit group
	Route::get('group/show-my-group', 'API\GroupController@showMyGroup'); // tampilkan group user
	Route::get('group/show/{id?}', 'API\GroupController@show'); // tampilkan group tertentu
	// profile route
	Route::post('profile', 'API\ProfileController@myProfile'); // show my profile
	Route::put('profile/update', 'API\ProfileController@updateProfile'); // change profile
	Route::post('profile/update-photo', 'API\ProfileController@updatePhoto'); //update photo profile
});
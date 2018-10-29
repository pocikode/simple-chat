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
Route::get('profile/{id?}', 'API\ProfileController@show');

// Passport route
Route::group(['middleware' => 'auth:api'], function(){
	Route::get('details', 'API\UserController@details');
	Route::group(['prefix' => 'message'], function(){
		Route::post('private/send', 'API\PrivateChatController@send');
		Route::post('private/show', 'API\PrivateChatController@show');
		Route::post('private/delete', 'API\PrivateChatController@delete');

	});
});
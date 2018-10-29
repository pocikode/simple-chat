<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{
	// login method
    function login()
    {
    	// check if phone number exists
    	$phone = User::where('phone',request('phone'))->first();
    	if (!empty($phone)) {
    		// if phone number is exists
    		Auth::login($phone); // create login
    		$user = Auth::user(); 
    		$success['token'] = $user->createToken('nApp')->accessToken; // create token from user data
    		return response()->json(['success' => $success], 200);
    	} else {
    		// if phone number is not exists, return error
    		return response()->json(['error' => 'Unauthorised'], 401);
    	}
    }

    // register method
    function register(Request $request)
    {
    	// form validation
    	$validator = Validator::make($request->all(), [
    		'name'	=> 'required',
    		'phone'	=> 'required',
    	]);


    	// move photo to public/images directory
    	// request()->photo_profile->move(public_path('images'), $photoName);s

    	// if validation fails
    	if ($validator->fails()) {
    		return response()->json(['error' => $validator->errors()], 401);
    	}

    	$input = $request->all();
		// $input['photo_profile'] = url('/images/' . $photoName);

    	$user = User::create($input);
    	$success['token'] = $user->createToken('nApp')->accessToken;
    	$success['name'] = $user->name;

    	return response()->json(['success'=>$success], 200);
    }

    function details()
    {
    	$user = Auth::user();
    	return response()->json(['success'=>$user], 200);
    }
}

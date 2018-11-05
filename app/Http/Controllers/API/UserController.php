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
    		$token = $user->createToken('nApp')->accessToken; // create token from user data
    		return response()->json(['success' => 'Berhasil Login!', 'token' => $token], 201);
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

    	// if validation fails
    	if ($validator->fails()) {
    		return response()->json(['error' => $validator->errors()], 401);
    	}

        $input = $request->all();
        $input['photo_profile'] = url('images/default-user-photo.png'); // user default photo_profile

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
	
	function show($id=null)
	{
		if(is_null($id)){
			$user = User::get()->all();
		}else{
			$user = User::where('user_id', $id)->first();
		}
    
    return response()->json($user,200);
	}
}

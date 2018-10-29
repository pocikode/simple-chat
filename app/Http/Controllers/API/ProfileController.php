<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Validator;

class ProfileController extends Controller
{
    public function show($id)
    {
    	$data = User::where('user_id',$id)->first();
    	return response()->json(['data'=>$data], 200);
    }

    // public function update(Request $request)
    // {
    // 	$photo = $request->file('photo_profile');
    // 	$extension = $photo->getClientOriginalExtension();

	// 	$validator = Validator::make($request->all(), [
    // 		'name'	=> 'required',
    // 		'phone'	=> 'required'
    // 	]);	
    	
    // 	if ($validator->fails()) {
    // 		return response()->json(['error' => $validator->errors()], 401);
    // 	}
    // }
}

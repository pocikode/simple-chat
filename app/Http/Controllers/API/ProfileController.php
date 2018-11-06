<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // show my profile
    public function myProfile()
    {
        $user = Auth::user();
    	return response()->json(['success'=>$user], 200);
    }

    // change photo profile
    public function updatePhoto(Request $request)
    {
        request()->validate([
            'photo_profile' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // ubah nama file
        $fileName = now()->timestamp .'-'. uniqid() .'.'. request()->photo_profile->getClientOriginalExtension();
        // simpan file ke public/images
        request()->photo_profile->move(public_path('images'), $fileName);

        // update data user
        $data = User::find(Auth::user()->user_id);
        $data->photo_profile = url('/images/'.$fileName);
        $data->save();

        return response()->json(['success' => 'foto berhasil diupload!'],200);
    }

    // update profile
    public function updateProfile(Request $request)
    {
        $message = [];

        request()->validate([
            'phone' => 'digits_between:9,15',
            'name'  => 'max:225'
        ]);

        if (!is_null($request->name)) {
            $profile = User::find(Auth::user()->user_id);
            $profile->name = $request->name;
            $message['name'] = 'Name chaged to '.$request->name;
            $profile->save(); // save
        }
        if (!is_null($request->phone)) {
            $profile = User::find(Auth::user()->user_id);
            $profile->phone = $request->phone;
            $message['phone'] = 'Phone changed to '.$request->phone;
            $profile->save(); // save
        }

        return response()->json(['success' => $message],200);
    }
}

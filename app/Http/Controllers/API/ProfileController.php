<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    // show my profile
    public function myProfile()
    {
        $user = Auth::user();
    	return response()->json($user, 200);
    }

    // change photo profile
    public function updatePhoto(Request $request)
    {
        request()->validate([
            'photo_profile' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // ubah nama file
        $fileName = Auth::user()->phone .'.'. request()->photo_profile->getClientOriginalExtension();
        // simpan file ke public/images
        request()->photo_profile->move(public_path('images'), $fileName);

        // update data user
        DB::table('users')->where('user_id', Auth::user()->user_id)
                          ->update(['photo_profile' => url('/images/'.$fileName)]);

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
            DB::table('users')->where('user_id', Auth::user()->user_id)
                              ->update(['name' => $request->name]);
            $message['name'] = 'Name chaged to '.$request->name;
        }
        if (!is_null($request->phone)) {
            DB::table('users')->where('user_id', Auth::user()
                            ->user_id)
                            ->update(['phone' => $request->phone]);
            $message['phone'] = 'Phone changed to '.$request->phone;
        }

        return response()->json(['success' => $message],200);
    }
}

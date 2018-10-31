<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Group;
use App\User;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    // buat grup baru
    public function create(Request $request)
    {
        $data = new Group;
        $data->name = $request->name;
        $data->member = json_encode([Auth::user()->user_id]); // simpan array dalam bentuk json
        $data->save();

        $message = 'Group ' . $request->name . ' berhasil dibuat';

        return response()->json(['success' => $message], 200);
    }

    // tambah user ke group
    public function addUser(Request $request)
    {
        // ambil data group berdasarkan group_id
        $group = Group::where('group_id', $request->group_id)->first();
        $member= json_decode($group->member); // ubah data ke array

        // cek data user
        $user = User::where('phone', $request->phone)->first();
        if (is_null($user)){
            // jika tidak ada user dengan no hp yang diinputkan
            return response()->json(['failed' => 'Tidak ada user dengan no hp '.$request->phone], 406);
        } else {
            // jika ada user
            // cek apakah user sudah ditambahkan
            if (in_array($user->user_id, $member)) {
                // jika user sudah ditambahkan sebelumnya
                return response()->json(['failed' => 'User sudah ditambahkan!'], 406);
            } else {
                // jika belum ditambahkan
                // tambahkan user ke group
                $member[] = $user->user_id;

                // update data
                $data = Group::find($request->group_id);
                $data->member = json_encode($member);
                $data->save();

                $message = $user->name.' berhasil ditambahkan ke group '.$group->name;
                return response()->json(['success'=>$message], 200);
            }
        }
    }

    // keluar dari group
    public function exit(Request $request)
    {
        // ambil data group
        $group = Group::where('group_id', $request->group_id)->first();
        $member= json_decode($group->member); // ubah data ke array

        // hapus user dari group
        if($key = array_search(Auth::user()->user_id, $member) !== false){
            $key=array_search(Auth::user()->user_id, $member);
            unset($member[$key]);

            // update data
            $data = Group::find($request->group_id);
            $data->member = json_encode(array_values($member));
            $data->save();

            $message = "success";
            return response()->json(['success'=>$message,'user_id'=>Auth::user()->user_id], 200);
        }
    }

    // show group info
    public function show($id = null)
    {
        $group = Group::where('group_id',$id)->first();
        $group->member = json_decode($group->member);
        return response()->json(['data'=>$group], 200);
    }

    // tampilkan group berdasarkan user
    public function showMyGroup()
    {
        $user_id = Auth::user()->user_id;
        
        // cari group dimana user bergabung
        $group = DB::table('groups')->where('member','['.$user_id.']')->orWhere('member','like','['.$user_id.',%')->orWhere('member','like','%,'.$user_id.',%')->orWhere('member','like','%,'.$user_id.']')->get();

        return response()->json(['data'=>$group],200);
    }
}
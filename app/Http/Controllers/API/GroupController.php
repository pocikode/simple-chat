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
    // menampilkan semua group user
    public function index()
    {
        $user_id = Auth::user()->user_id;
        // cari group dimana user bergabung
        $group = DB::table('groups')->where('member', '[' . $user_id . ']')
                                    ->orWhere('member', 'like', '[' . $user_id . ',%')
                                    ->orWhere('member', 'like', '%,' . $user_id . ',%')
                                    ->orWhere('member', 'like', '%,' . $user_id . ']')
                                    ->get();

        return response()->json($group, 200);
    }

    // menampilkan info group tertentu dimana user bergabung
    public function show($id)
    {
        $group = Group::where('group_id', $id)->first();
        // cek apakah user tergabung dengan grup tsb atau tidak
        if (!in_array(Auth::user()->user_id, json_decode($group->member))) {
            // jika user belum tergabung, request ditolak
            return response()->json(['error' => 'Not Accepted!'], 406);
        }

        return response()->json($group, 200);
    }

    // buat grup baru
    public function store(Request $request)
    {
        $data = new Group;
        $data->name = $request->name;
        $data->member = json_encode([Auth::user()->user_id]); // simpan array dalam bentuk json
        $data->save();

        $message = 'Group ' . $request->name . ' berhasil dibuat';

        return response()->json(['success' => $message], 200);
    }

    // tambah user ke group
    public function update($group_id, Request $request)
    {
        // ambil data group berdasarkan group_id
        $group = Group::where('group_id', $group_id)->first();
        $member= json_decode($group->member); // ubah data ke array

        // validate
        if (!in_array(Auth::user()->user_id, $member)) {
            return response()->json(['error' => 'Not Accepted!'], 406);
        }

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
                DB::table('groups')->where('group_id', $group_id)
                                   ->update(['member' => json_encode($member)]);

                $message = $user->name.' berhasil ditambahkan ke group '.$group->name;
                return response()->json(['success'=>$message], 200);
            }
        }
    }

    // keluar dari group
    public function destroy($group_id)
    {
        // ambil data group
        $group = Group::where('group_id', $group_id)->first();
        $member= json_decode($group->member); // ubah data ke array

        // validate
        if(!in_array(Auth::user()->user_id, $member)) {
            return response()->json('',406);
        }

        // hapus user dari group
        if($key = array_search(Auth::user()->user_id, $member) !== false){
            $key=array_search(Auth::user()->user_id, $member);
            unset($member[$key]);

            // update data
            DB::table('groups')->where('group_id', $group_id)
                               ->update(['member' => json_encode(array_values($member))]);

            // jika member kosong, hapus group
            if (count($member) === 0) {
                Group::find($group_id)->delete();
            }

            $message = "success";
            return response()->json(['success'=>$message,'user_id'=>Auth::user()->user_id], 200);
        }
    }

    // TEST
    // menampilkan semua group yang ada di database
    function showAll()
    {
        $groups = Group::get()->all();
        return response()->json($groups);
    }
}
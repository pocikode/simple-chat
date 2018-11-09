<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GroupChat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Group;
use function GuzzleHttp\json_decode;

class GroupChatController extends Controller
{
    // menampilkan semua group chat user
    public function index()
    {
        $user_id = Auth::user()->user_id;

        // cek di group dimana saja user bergabung
        $groups = DB::table('groups')->where('member', '[' . $user_id . ']')
            ->orWhere('member', 'like', '[' . $user_id . ',%')
            ->orWhere('member', 'like', '%,' . $user_id . ',%')
            ->orWhere('member', 'like', '%,' . $user_id . ']')
            ->pluck('group_id');

        // tampilkan pesan dari group user
        $chats = DB::table('group_chats')->whereIn('group_id', $groups)->get();
        return response()->json($chats);
    }

    // menampilkan semua chat dari group tertentu
    public function show($group_id)
    {
        $group = Group::where('group_id', $group_id)->first();
        // validate group
        if (!in_array(Auth::user()->user_id, json_decode($group->member))) {
            // jika user tidak tergabung dengan group, request ditolak
            return response()->json(['error' => 'Not Acceptable!'], 406);
        }

        $chats = GroupChat::where('group_id', $group_id)->get();
        return response()->json($chats, 200);
    }

    // send message to group
    public function store(Request $request)
    {
        $group = Group::where('group_id', $request->group_id)->first();
        // validate group
        if (!in_array(Auth::user()->user_id, json_decode($group->member))) {
            return response()->json(['error' => 'Not Acceptable!'], 406);
        }

        $data = new GroupChat;
        $data->group_id = $request->group_id;
        $data->user_id  = Auth::user()->user_id;
        $data->message  = $request->message;
        $data->save();

        $success = [
            'group_id'  => $request->group_id,
            'user_id'   => Auth::user()->user_id,
            'message'   => $request->message,
            'created_at'=> date('Y-m-d H:i'),
            'status'    => 'sent'
        ];

        return response()->json(['success'=>$success],200);
    }

    // delete chat
    function destroy($group_chat_id)
    {
        $chat = GroupChat::find($group_chat_id);
        // validate
        if($chat->user_id != Auth::user()->user_id)
        {
            // jika yang mengirim bukan dari user, request ditolak
            return response()->json(['error' => 'Not Acceptable'], 406);
        } else {
            $chat->delete();
            return response()->json(['success' => 'Deleted!']);
        }
    }
}

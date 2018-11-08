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
    // show group chat by id
    public function show(Request $request, $group_id=null)
    {
        $user_id = Auth::user()->user_id;

        if (is_null($group_id)) {
            $groups = DB::table('groups')->where('member', '[' . $user_id . ']')
                                         ->orWhere('member', 'like', '[' . $user_id . ',%')
                                         ->orWhere('member', 'like', '%,' . $user_id . ',%')
                                         ->orWhere('member', 'like', '%,' . $user_id . ']')
                                         ->pluck('group_id');

            $chats = DB::table('group_chats')->whereIn('group_id', $groups)->get();

            return response()->json($chats);
        } else {
            $group = Group::where('group_id', $request->group_id)->first();
            // validate group
            if (!in_array($user_id, json_decode($group->member))) {
                return response()->json(['error' => 'Not Acceptable!'], 406);
            }

            $chats = GroupChat::where('group_id', $request->group_id)->get();
            return response()->json($chats, 200);
        }
    }

    // send message to group
    public function send(Request $request)
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
    function delete(Request $request)
    {
        $chat = GroupChat::find($request->group_chat_id);
        // validate
        if($chat->user_id != Auth::user()->user_id)
        {
            return response()->json(['error' => 'Not Acceptable'], 406);
        } else {
            $chat->delete();
            return response()->json(['success' => 'Deleted!']);
        }
    }
}

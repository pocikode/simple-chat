<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GroupChat;
use Illuminate\Support\Facades\Auth;

class GroupChatController extends Controller
{
    // show group chat by id
    public function show(Request $request)
    {
        $chat = GroupChat::where('group_id', $request->group_id)->get();
        return response()->json(['data'=>$chat],200);
    }

    // send message to group
    public function send(Request $request)
    {
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
}

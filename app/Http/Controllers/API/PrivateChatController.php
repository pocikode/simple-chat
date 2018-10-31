<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\PrivateChat;
use Illuminate\Support\Facades\DB;

class PrivateChatController extends Controller
{
    // show all user private chats
    public function show()
    {
        $message = DB::table('private_chats')->where('user_id',Auth::user()->user_id)->orWhere('to_user',Auth::user()->user_id)->get(); 
        return response()->json(['data'=>$message], 200);
    }

    public function send(Request $request)
    {
        $data = new PrivateChat;
        $data->user_id  = Auth::user()->user_id;
        $data->to_user  = $request->to_user;
        $data->message  = $request->message;
        $data->save();

        $success = [
            'user_id'   => $data->user_id,
            'to_user'   => $data->to_user,
            'message'   => $data->message,
            'created_at'=> date('Y-m-d H:i'),
            'status'    => 'sent'
        ];

        return response()->json(['success'=>$success], 200);
    }

    public function delete(Request $request)
    {
        $data = PrivateChat::find($request->private_chat_id);
        $data->delete();

        return response()->json(['success' => 'Deleted!'], 200);
    }
}

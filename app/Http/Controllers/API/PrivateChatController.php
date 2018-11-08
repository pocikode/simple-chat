<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\PrivateChat;
use Illuminate\Support\Facades\DB;
use App\User;

class PrivateChatController extends Controller
{
    // show all user private chats
    public function index()
    {
        $message = DB::table('private_chats')->where('user_id',Auth::user()->user_id)->orWhere('to_user',Auth::user()->user_id)->get(); 
        return response()->json($message, 200);
    }

    public function store(Request $request)
    {
        $to_user = User::where('phone',$request->phone)->first();

        $data = new PrivateChat;
        $data->user_id  = Auth::user()->user_id;
        $data->to_user  = $to_user->user_id;
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

    public function destroy($private_chat_id)
    {
        $chat = PrivateChat::find($private_chat_id);
        // validate
        if ($chat->user_id != Auth::user()->user_id) {
            return response()->json(['error' => 'Not Acceptable'], 406);
        } else {
            $chat->delete();
            return response()->json(['success' => 'Deleted!'], 200);
        }
    }
}

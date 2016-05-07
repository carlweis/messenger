<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Conversation;

class ConversationsController extends Controller
{
	public function index()
	{
		// return all conversations for the given user
		return response()->json(
			Conversation::with('messages')->where([
				['sender_id', '=', Auth::guard('api')->id()],
				['recipient_id', '=', Auth::guard('api')->id()]
			])->get()
		);
	}

    public function store(Request $request)
    {
    	$data = $request->input();

    	// check if an existing conversation exists
    	$conversation = Conversation::where([
    		'sender_id' => $data['sender_id'],
    		'recipient_id' => $data['recipient_id']
		])->first();

		// if so return the conversation_id
		if ($conversation) {
			return response()->json(['conversation_id' => $conversation->id]);
		}

    	// if not create a new conversation and return it's id
    	$conversation = Conversation::create([
    		'sender_id' => $data['sender_id'],
    		'recipient_id' => $data['recipient_id']
		]);
    	return response()->json(
    		['conversation_id' => $conversation->id]
		);
    }
}

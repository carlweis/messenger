<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Conversation;
use Carbon\Carbon;
use DateTime;

class ConversationsController extends Controller
{
	public function __construct()
	{
		Carbon::setToStringFormat(DateTime::ISO8601);
	}

	public function index()
	{
		// return all conversations for the given user
		return response()->json(
			Conversation::with(['messages.user', 'sender', 'recipient'])->where([
				['sender_id', '=', Auth::guard('api')->id()]
			])->orWhere([
				['recipient_id', '=', Auth::guard('api')->id()]
			])->orderBy('created_at', 'asc')->get()
		);
	}

	public function show(Request $request, $id)
	{
		return response()->json(
			Conversation::with(['messages.user', 'sender', 'recipient'])->where([
				['id', '=', $id]
			])->first()
		);
	}

    public function store(Request $request)
    {
    	$data = $request->input();

    	// check if an existing conversation exists
    	$conversation = Conversation::where([
    		'sender_id' => $data['sender_id'],
    		'recipient_id' => $data['recipient_id']
		])->orWhere([
			'recipient_id' => $data['sender_id'],
    		'sender_id' => $data['recipient_id']
		])->first();

		// if so return the conversation_id
		if (!$conversation) {
			// if not create a new conversation and return it's id
	    	$conversation = Conversation::create([
	    		'sender_id' => $data['sender_id'],
	    		'recipient_id' => $data['recipient_id'],
			]);
		}
    	return response()->json([
			'conversation' =>  Conversation::with(['messages', 'sender', 'recipient'])->where('id',$conversation->id)->first()
		]);
    }
}

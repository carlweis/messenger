<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Message;
use Carbon\Carbon;
use DateTime;

class MessagesController extends Controller
{
	public function __construct()
	{
		Carbon::setToStringFormat(DateTime::ISO8601);
	}

	public function index()
	{
		// return all messages for the given user
		return response()->json(
			Message::with(['user', 'conversation'])
				->where([
					['user_id', '=', Auth::guard('api')->id()]
				])->orderBy('created_at', 'desc')->get()
		);
	}

    public function store(Request $request)
    {
    	// validate the request
    	$this->validate($request, [
    		'user_id' => 'required|numeric',
    		'conversation_id' => 'required|numeric',
    		'body' => 'required',
		]);

    	// get the request data
    	$data = $request->input();

    	// create a new message
    	$message = Message::create([
    		'user_id' => $data['user_id'],
    		'conversation_id' => $data['conversation_id'],
    		'body' => $data['body']
		]);
		return response()->json([
			'message' => Message::with(['user', 'conversation'])->where('id', $message->id)->first()
		]);
    }
}

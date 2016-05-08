<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;

class UsersController extends Controller
{
    public function index()
    {
    	return response()->json(
    		User::all()
		);
    }

    public function show($user)
    {
    	return response()->json(
    		User::where('id', $user)->first()
		);
    }
}

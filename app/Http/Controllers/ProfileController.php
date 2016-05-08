<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;

class ProfileController extends Controller
{
    public function show(Request $request, $username)
    {
    	$user = User::where([
    		['name', '=', $username]
		])->first();

		if ($user) {
			return view('users.profile', compact('user'));
		}
		return redirect('/');
    }
}

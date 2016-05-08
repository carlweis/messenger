<?php

namespace App;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['user_id', 'conversation_id', 'body', 'subject'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function conversation()
    {
    	return $this->belongsTo(Conversation::class, 'conversation_id', 'id');
    }

    public function user()
    {
    	return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getCreatedAtAttribute($date)
	{
	    if(Auth::check())
	        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->copy()->tz(Auth::user()->timezone)->format('F j, Y @ g:i A');
	    else
	        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->copy()->tz('America/Chicago')->format('F j, Y @ g:i A');
	}

	public function getUpdatedAtAttribute($date)
	{
	    return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('F j, Y @ g:i A');
	}
}

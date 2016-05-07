<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['sender_id', 'recipient_id'];

    public function sender()
    {
    	return $this->belongsTo(User::class, 'id', 'sender_id');
    }

    public function recipient()
    {
    	return $this->belongsTo(User::class, 'id', 'recipient_id');
    }

    public function messages()
    {
    	return $this->hasMany(Message::class);
    }
}

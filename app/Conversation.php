<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['sender_id', 'recipient_id'];

    protected $casts = [
        'active' => 'boolean',
        'archived' => 'boolean',
        'blocked' => 'boolean',
        'favorite' => 'boolean'
    ];

    public function sender()
    {
    	return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function recipient()
    {
    	return $this->belongsTo(User::class, 'recipient_id', 'id');
    }

    public function messages()
    {
    	return $this->hasMany(Message::class, 'conversation_id', 'id');
    }
}

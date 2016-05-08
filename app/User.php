<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'api_token', 'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_key'
    ];

    public function sentConversations()
    {
        return $this->hasMany(Conversation::class, 'sender_id', 'id');
    }

    public function receivedConversations()
    {
        return $this->hasMany(Conversation::class, 'recipient_id', 'id');
    }

    public function conversations()
    {
        return $this->receivedConversations();
        // return $this->hasMany(Conversation::class, 'id', 'sender_id');
    }
}

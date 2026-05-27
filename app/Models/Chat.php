<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['care_request_id', 'user_id', 'creator_id'];

    public function careRequest()
    {
        return $this->belongsTo(CareRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Caregiver
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id'); // Owner
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function unreadMessagesCount($userId)
    {
        return $this->messages()->where('sender_id', '!=', $userId)->whereNull('read_at')->count();
    }

    /**
     * Get the chat partner for a given user.
     */
    public function getPartner($userId)
    {
        return $this->user_id == $userId ? $this->creator : $this->user;
    }
}

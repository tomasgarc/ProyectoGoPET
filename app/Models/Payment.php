<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'care_request_id',
        'user_id',
        'receiver_id',
        'amount',
        'fee',
        'net_amount',
        'status', // escrow, released, refunded
        'card_last_four',
        'transaction_id',
    ];

    /**
     * Get the care request associated with the payment.
     */
    public function careRequest()
    {
        return $this->belongsTo(CareRequest::class);
    }

    /**
     * Get the user who paid (the owner).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who received the payment (the caretaker).
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}

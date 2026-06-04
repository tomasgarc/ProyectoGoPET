<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'care_request_id',
        'reviewer_id',
        'reviewee_id',
        'rating',
        'comment',
    ];

    /**
     * Get the care request associated with the review.
     */
    public function careRequest()
    {
        return $this->belongsTo(CareRequest::class);
    }

    /**
     * Get the user who wrote the review.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get the user who is being reviewed.
     */
    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}

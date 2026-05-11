<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = ['title', 'description', 'user_id', 'dog_id', 'status'];

    /**
     * Get the owner who posted the advertisement.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the dog mentioned in the advertisement.
     */
    public function dog()
    {
        return $this->belongsTo(Dog::class);
    }
}

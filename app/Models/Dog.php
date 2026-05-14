<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dog extends Model
{
    protected $fillable = ['name', 'breed', 'age', 'size', 'photo', 'user_id'];

    /**
     * Get the owner of the dog.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the advertisements for the dog.
     */
    /**
     * Get the advertisements for the dog.
     */
    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }

    /**
     * Get the care requests for the dog.
     */
    public function careRequests()
    {
        return $this->belongsToMany(CareRequest::class);
    }
}

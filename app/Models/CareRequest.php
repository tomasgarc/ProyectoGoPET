<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareRequest extends Model
{
    protected $fillable = ['user_id', 'start_date', 'end_date', 'price', 'description', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dogs()
    {
        return $this->belongsToMany(Dog::class);
    }
}

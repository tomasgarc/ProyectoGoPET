<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareRequest extends Model
{
    protected $fillable = ['user_id', 'start_date', 'end_date', 'price', 'description', 'status', 'accepted_by'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function dogs()
    {
        return $this->belongsToMany(Dog::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'care_request_favorites')->withTimestamps();
    }

    public function isFavoritedBy($userId): bool
    {
        return $this->favoritedByUsers()->where('user_id', $userId)->exists();
    }

    /**
     * Check if the care request has expired/finalized.
     */
    public function isFinalized(): bool
    {
        return $this->end_date < now()->toDateString() || $this->status === 'finalized';
    }

    /**
     * Get the resolved status of the care request.
     */
    public function getResolvedStatus(): string
    {
        if ($this->isFinalized()) {
            return 'finalized';
        }
        return $this->status;
    }

    /**
     * Get resolved status label for display in UI.
     */
    public function getStatusLabel(): string
    {
        $status = $this->getResolvedStatus();
        return match ($status) {
            'pending' => 'Normal',
            'accepted' => 'Aceptada',
            'finalized' => 'Finalizada',
            default => ucfirst($status),
        };
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get the user's avatar URL or a default fallback.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Hermoso avatar por defecto con iniciales
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the dogs owned by the user.
     */
    public function dogs()
    {
        return $this->hasMany(Dog::class);
    }

    /**
     * Get the advertisements posted by the user.
     */
    /**
     * Get the care requests posted by the user.
     */
    public function careRequests()
    {
        return $this->hasMany(CareRequest::class);
    }

    /**
     * Get the care requests favorited by the user.
     */
    public function favoriteCareRequests()
    {
        return $this->belongsToMany(CareRequest::class, 'care_request_favorites')->withTimestamps();
    }

    /**
     * Get the payments made by the user (as owner).
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    /**
     * Get the payments received by the user (as caretaker).
     */
    public function receivedPayments()
    {
        return $this->hasMany(Payment::class, 'receiver_id');
    }
}

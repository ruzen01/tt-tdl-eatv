<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
     * Check if the profile is public
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->status === 'Public';
    }

    /**
     * Check if the profile is private
     *
     * @return bool
     */
    public function isPrivate(): bool
    {
        return $this->status === 'Private';
    }

    /**
     * Check if the given user can view this profile
     *
     * @param User|null $viewer
     * @return bool
     */
    public function canViewProfile(?User $viewer = null): bool
    {
        if ($this->isPublic()) {
            return true;
        }

        if (! $viewer) {
            return false;
        }

        return $this->id === $viewer->id;
    }

    /**
     * Get the todo lists for the user.
     */
    public function todolists()
    {
        return $this->hasMany(TodoList::class);
    }
}

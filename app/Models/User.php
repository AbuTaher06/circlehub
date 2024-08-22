<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile',
        'cover_photo',
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
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the posts for the user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the comments for the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the user's friends.
     */
    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
                    ->withTimestamps()
                    ->select('users.*'); // Specify that you want to select all columns from the users table
    }

    /**
     * Get mutual friends between the user and another user.
     */
    public function mutualFriends(User $otherUser): Collection
    {
        return $this->friends()
                    ->whereIn('users.id', $otherUser->friends()->pluck('users.id'))
                    ->get();
    }

    /**
     * Get recently added friends.
     */
    public function recentlyAddedFriends(): Collection
    {
        return $this->friends()
                    ->orderBy('friendships.created_at', 'desc')
                    ->select('users.*', 'friendships.created_at as friendship_created_at') // Select the users columns and friendship created_at
                    ->take(5)
                    ->get();
    }

    /**
     * Get the friendships where the user is the owner.
     */
    public function friendships(): HasMany
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    /**
     * Get the friendships where the user is the friend.
     */
    public function friendOf(): HasMany
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }

    /**
     * Get the activities for the user.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}

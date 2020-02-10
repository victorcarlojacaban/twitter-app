<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'avatar',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tweets()
    {
      return $this->hasMany(Tweet::class);
    }

    public function getRouteKeyName()
    {
        return 'first_name';
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id')
                    ->withPivot('follower_id', 'user_id')
                    ->withTimestamps();
    }

     public function getFirstNameAndLastnameAttribute()
    {
        return $this->first_name .' '. $this->last_name;
    }

    public function getProfileLinkAttribute()
    {
        return route('user.show', $this);
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    } 

    public function isNot($user)
    {
        return $this->id !== $user->id;
    }

    public function isFollowing($user)
    {
        return (bool) $this->following->where('id', $user->id)->count();
    }

    public function canFollow($user)
    {
        if(!$this->isNot($user)) {
            return false;
        }
        return !$this->isFollowing($user);
    }

    public function canUnFollow($user)
    {
      return $this->isFollowing($user);
    }

    public function likes()
    {
       return $this->belongsToMany( 'App\Tweet', 'likes', 'user_id', 'tweet_id');
    }

    // public function likedTweet($tweetId)
    // {
    //     $likedTweet = $this->likes()->where([
    //         'tweet_id'   => $tweetId,
    //     ])->first();

    //     return !is_null($likedTweet);
    // }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{   
    protected $with = ['user'];

	protected $guarded = [];
	
    protected $appends = ['createdDate'];

    public function user()
    {
     return $this->belongsTo(User::class);
    }

    public function getCreatedDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function likes()
    {
        return $this->belongsToMany('App\User', 'likes', 'tweet_id', 'user_id');
    }
}

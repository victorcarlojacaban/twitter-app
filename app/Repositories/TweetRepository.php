<?php

namespace App\Repositories;

use App\Like;
use App\Tweet;

/**
 * Class TweetRepository.
 */
class TweetRepository
{
    /**
     * @var Tweet
     */
    protected $model;

    /**
     * Tweet constructor.
     *
     * @param Account $account
     */
    public function __construct(Tweet $tweet)
    {
        $this->model = $tweet;
    }

    /**
     * Get tweets data
     * 
     * @param  object request request data
     * 
     * @return array
     */
    public function getTweets($request)
    {
        $items = $this->model->whereIn('user_id', $request->user()->following()
            ->pluck('users.id')
            ->push($request->user()->id))
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take($request->get('limit', 10));

        if ($hashSearch = $request->hashSearch) {
            $items->where('hash_tag', 'like', "%$hashSearch%")
                ->orWhere('text', 'like', "%$hashSearch%");

        }

        $itemsInstance = $items->get();

        // map check if user liked specific tweet
        $itemsInstance->map(function ($tweet) {
            $likedTweet = Like::where('tweet_id', '=', $tweet->id)
                        ->where('user_id', '=', auth()->user()->id)
                        ->first();

            $tweet['liked'] = $likedTweet;

            return $tweet;
        });

        return $itemsInstance;
    }

}

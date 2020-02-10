<?php

namespace App\Http\Controllers;

use App\Tweet;
use App\Like;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TweetController extends Controller
{
    /**
     * Index page
     * 
     * @param  Request $request request data
     * @param  Tweet   $tweet   tweet instance
     * 
     * @return array
     */
    public function index(Request $request, Tweet $tweet)
    {
        if (!$request->ajax()) abort( 404 );

        $items = $tweet->whereIn('user_id', $request->user()->following()
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
                        ->where('user_id', '=', Auth::user()->id)
                        ->first();

            $tweet['liked'] = $likedTweet;

            return $tweet;
        });
        
        return ['items' => $itemsInstance];
    }

    /**
     * Store resource
     * 
     * @param  Request $request request data
     * 
     * @return json
     */
    public function store(Request $request)
    {
        $request->validate([
            'text'  => 'required|string|max:1000',
            'photo' => 'required|image64:jpeg,jpg,png',
        ]);

        if ($imageData = $request->get('photo')) {
            $name = getTwitName($imageData);

            $filePath = config('image_path.twit_post') . $name;

           \Image::make($imageData)->save(getPostImgPublicPath($name));

           $request->photo = $filePath;
        }

        $newTweet = $request->user()->tweets()->create([
            'text' => $request->text,
            'photo' => $request->photo,
            'hash_tag' => $request->hash_tag
        ]);

        return response()->json($newTweet);
    }

    /**
     * Update specified resource
     * 
     * @param  Request $request request data
     * @param  array  $item  tweet id
     * 
     * @return json
     */
    public function update(Request $request, $item)
    {
        $request->validate([
            'text'  => 'required|string|max:1000',
        ]);

        // Get selected tweet
        $tweet = Tweet::findOrFail($item);

        $tweet->text     = $request->input('text');
        $tweet->hash_tag = $request->input('hash_tag');
        $photo           = $tweet->photo;

        if (!is_null($request->get('photo'))) {
            $request->validate([
                'photo' => 'required|image64:jpeg,jpg,png',
            ]);

            $imageData = $request->get('photo');

            $name = getTwitName($imageData);

            $filePath = config('image_path.twit_post') . $name;

           \Image::make($imageData)->save(getPostImgPublicPath($name));

           // delete previous photo
           \File::delete(public_path($photo));

           $photo = $filePath;
        }

        $tweet->photo = $photo;

        $tweet = $tweet->update();

        return response()->json($tweet);

    }

    /**
     * Delete specific resource
     * 
     * @param  integer $item item data
     * 
     * @return json
     */
    public function destroy($item)
    {
        Tweet::find($item)->delete();

        return response()->json(['ok']);
    }
}

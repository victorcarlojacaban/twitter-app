<?php

namespace App\Http\Controllers;

use App\Tweet;
use App\Repositories\TweetRepository;
use Illuminate\Http\Request;

class TweetController extends Controller
{
    /** @var tweetRepo */
    protected $tweetRepo;

    /**
     * @param TweetRepository $tweetRepo
     */
    public function __construct(TweetRepository $tweetRepo)
    {
        $this->tweetRepo = $tweetRepo;
    }

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

        $items = $this->tweetRepo->getTweets($request);
        
        return ['items' => $items];
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

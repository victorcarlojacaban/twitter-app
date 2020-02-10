<?php

namespace App\Http\Controllers;

use App\User;
use App\Tweet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Traits\UploadTrait;

class UserController extends Controller
{
    use UploadTrait;

    /**
     * Show User
     * 
     * @param  User  $user user data
     * 
     * @return view
     */
    public function show(User $user)
    {
        return view('user', compact('user'));
    }

    /**
     * Profile Page
     * 
     * @return view
     */
    public function profile()
    {
        return view('profile');
    }

    /**
     * Profile Information
     * 
     * @return json
     */
    public function profileInfo()
    {
        $userData = Auth::user();

        $user = [
            'tweetCount' => count($userData->tweets),
            'followingCount' => count($userData->following),
            'followersCount' => count($userData->followers),
        ];

        return response()->json($user);
    }

    /**
     * Update Profile
     * 
     * @param  Request $request reqeuest data
     * 
     * @return redirect
     */
    public function updateProfile(Request $request)
    {
        // Get current user
        $user = User::findOrFail(Auth::user()->id);

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => 'required|email|unique:users,email,'.$user->id,
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user->first_name = $request->input('first_name');
        $user->last_name  = $request->input('last_name');
        $user->email      = $request->input('email');

        // Check if a avatar image has been uploaded
        if ($request->has('avatar')) {
            $image = $request->file('avatar');
            // Make a image name based on user name and current timestamp
            $name = \Str::slug($request->input('first_name')).'_'.time();

            $folder = config('image_path.avatar');

            $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
            // Upload image
            $this->uploadOne($image, $folder, 'public', $name);

            // Set user avatar image path in database to filePath
            $user->avatar = $filePath;
        }

        $user->save();

        return redirect()->back()->with(['status' => 'Profile updated successfully.']);
    }

    /**
     * Delete and logout account
     * 
     * @return redirect
     */
    public function deletProfile()
    {
        // find current user
        $user = User::find(Auth::user()->id);

        // logout user
        Auth::logout();

        // delete and redirect to login
        if ($user->delete()) {
             return redirect('login');
        }
    }

    /**
     * Follow specific user
     * 
     * @param  Request $request request data
     * 
     * @param  User    $user    user instance
     * 
     * @return redirect
     */
    public function follow(Request $request, User $user)
    {
       if($request->user()->canFollow($user)) {
           $request->user()->following()->attach($user);
       }
       return redirect()->back();
    }

    /**
     * Unfollow specific user
     * 
     * @param  Request $request request data
     * @param  User    $user    user instance
     * 
     * @return redirect
     */
    public function unFollow(Request $request, User $user)
    {
       if($request->user()->canUnFollow($user)) {
        
           $request->user()->following()->detach($user);
        }
       
       return redirect()->back();
    }

    /**
     * Like specific Tweet
     * 
     * @param  tweet Id tweet id
     * 
     * @return json
     */
    public function likeTweet($tweetId)
    {
        $tweet = Tweet::where('id', '=', $tweetId)->first();

        $tweet->likes()->attach(Auth::user()->id, [
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        return response()->json( ['liked' => true], 201);
    }

    /**
     * UnLike specific Tweet
     * 
     * @param  tweet Id tweet id
     * 
     * @return json
     */
    public function unlikeTweet($tweetId)
    {
        $tweet = Tweet::where('id', '=', $tweetId)->first();

        $tweet->likes()->detach(Auth::user()->id);

        return response(null, 204);
    }
}

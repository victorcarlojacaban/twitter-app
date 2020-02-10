<?php


Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// users
Route::get('users/{user}', 'UserController@show')->name('user.show');
Route::get('profile-info', 'UserController@profileInfo')->name('user.profile-info');
Route::get('users/{user}/follow', 'UserController@follow')->name('user.follow');
Route::get('users/{user}/unfollow', 'UserController@unfollow')->name('user.unfollow');
Route::get('profile', 'UserController@profile')->name('user.profile');
Route::patch('users/update', 'UserController@updateProfile')->name('user-update.profile');
Route::get('/users-delete/', 'UserController@deletProfile')->name('user-delete.profile');
Route::post('tweet/like/{tweetId}', 'UserController@likeTweet')->name('tweet.like');
Route::post('tweet/unlike/{tweetId}', 'UserController@unlikeTweet')->name('tweet.unlike');

// tweet
Route::get('items', 'TweetController@items');
Route::resource('tweet-items', 'TweetController');

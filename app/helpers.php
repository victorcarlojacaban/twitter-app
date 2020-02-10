<?php

use Carbon\Carbon;

/*
 * Global helpers file with misc functions.
 */
if (!function_exists('getTwitName')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function getTwitName($imageData)
    {
        return Carbon::now()->timestamp . '_' . uniqid() . '.' . explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
    }
}

/*
 * Global helpers file with misc functions.
 */
if (!function_exists('getPostImgPublicPath')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function getPostImgPublicPath($name)
    {
        return public_path('images/tweet_post/').$name;
    }
}
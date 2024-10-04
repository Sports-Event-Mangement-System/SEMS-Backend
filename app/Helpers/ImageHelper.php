<?php

namespace App\Helper;

use Illuminate\Support\Facades\URL;

class ImageHelper
{
    public static function generateImageUrls($images)
    {
        $imageFilenames = is_array($images) ? $images : json_decode($images, true);
        return array_map(function ($filename) {
            return url('uploads/tournaments/'.$filename);
        }, $imageFilenames);
    }
}

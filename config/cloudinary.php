<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Cloudinary settings. Cloudinary is a cloud
    | service that offers a solution to a web application's entire image
    | management pipeline.
    |
    */

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'secure' => true, // Use HTTPS

    /*
    |--------------------------------------------------------------------------
    | Cloudinary URL
    |--------------------------------------------------------------------------
    |
    | If you prefer to use the Cloudinary URL instead of the individual components,
    | you can set it here.
    |
    */
    // 'cloud_url' => env('CLOUDINARY_URL'),

    /*
    |--------------------------------------------------------------------------
    | Upload Preset
    |--------------------------------------------------------------------------
    |
    | Upload presets allow you to define the default behavior for all uploads.
    |
    */
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', 'ml_default'),

    /*
    |--------------------------------------------------------------------------
    | Default Upload Folder
    |--------------------------------------------------------------------------
    |
    | Here you may configure the default folder for your uploads.
    |
    */
    'upload_folder' => env('CLOUDINARY_UPLOAD_FOLDER', ''),

    /*
    |--------------------------------------------------------------------------
    | Image Transformation
    |--------------------------------------------------------------------------
    |
    | Here you may configure the default image transformations.
    |
    */
    'transformations' => [
        'quality' => 'auto',
        'fetch_format' => 'auto',
        'crop' => 'limit',
        // 'width' => 500,
        // 'height' => 500,
    ],

];

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Compression Settings
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk kompresi gambar otomatis
    |
    */

    'enabled' => env('IMAGE_COMPRESSION_ENABLED', true),

    'quality' => env('IMAGE_COMPRESSION_QUALITY', 75),

    'max_width' => env('IMAGE_COMPRESSION_MAX_WIDTH', 1280),

    'max_height' => env('IMAGE_COMPRESSION_MAX_HEIGHT', 720),

    'format' => env('IMAGE_COMPRESSION_FORMAT', 'webp'), // webp, jpg, png

    'logging' => env('IMAGE_COMPRESSION_LOGGING', true),

];

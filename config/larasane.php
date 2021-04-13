<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Maximum input length
    |--------------------------------------------------------------------------
    |
    | To protect your application memory Larasane will truncate any input that
    | exceeds the following character number. Don't worry too much about this
    | this default length, you can conveniently change the value at runtime.
    |
    */

    'max_length' => 1000,

    /*
    |--------------------------------------------------------------------------
    | Code allowance
    |--------------------------------------------------------------------------
    |
    | Larasane only allows for basic inoffensive tags in HTML. While this can
    | be enough for most apps, you can allow for more tags and have control
    | over each tag attributes. Anyway consider starting from the bottom.
    |
    | See: https://github.com/tgalopin/html-sanitizer/blob/1.4.0/docs/1-getting-started.md#extensions
    |
    */

    'allow_code' => [
        'basic', /* 'list', 'table', 'image', 'code', 'iframe', 'details', 'extra' */
    ],

    /*
    |--------------------------------------------------------------------------
    | Links security
    |--------------------------------------------------------------------------
    |
    | Links are allowed in <a>, <img> and <iframe> tags, if these are enabled.
    | You can only allow links pointing outside your app, enforce HTTPS when
    | on production, allow image data in the image link, and allow mailto.
    |
    */

    'security' => [
        'enforce_hosts' => null, // If null, all links are allowed.
        'enforce_https' => null, // If null, enforce only on production.
        'image_data'    => false,
        'allow_mailto'  => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Tags and Attributes
    |--------------------------------------------------------------------------
    |
    | You can have fine control of which attribute to allow on each sanitized
    | tag. The underlying parsing extensions allow for only some inoffensive
    | attributes but you can override the default list of each tag in here.
    */

    'tags' => [
        'div' => 'class',
        'img' => ['src', 'alt', 'title', 'class'],
        'a' => ['class', 'target'],
        'ul' => 'class',
        'ol' => 'class',
        'li' => 'class',
    ]
];
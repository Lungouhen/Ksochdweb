<?php

return [
    'meta' => [
        'defaults'       => [],      // The default attributes that are always rendered.
        'webmaster_tags' => [
            'google'    => null,
            'bing'      => null,
            'alexa'     => null,
            'pinterest' => null,
            'yandex'    => null,
            'norton'    => null,
        ],
    ],
    'opengraph' => [
        'defaults'     => [],          // The default attributes that are always rendered.
        'url'          => false,       // Set to true or function/method to return the URL.
        'type'         => false,       // Set to true or function/method to return the type.
        'site_name'    => false,       // Set to true or function/method to return the site name.
        'title'        => false,       // Set to true or function/method to return the title.
        'description'  => false,       // Set to true or function/method to return the description.
        'image'        => false,       // Set to true or function/method to return the image.
        'locale'       => false,       // Set to true or function/method to return the locale.
    ],
    'twitter' => [
        'defaults'      => [],          // The default attributes that are always rendered.
        'card'          => false,       // Set to true or function/method to return the card.
        'site'          => false,       // Set to true or function/method to return the site username.
        'creator'       => false,       // Set to true or function/method to return the creator username.
        'title'         => false,       // Set to true or function/method to return the title.
        'description'   => false,       // Set to true or function/method to return the description.
        'image'         => false,       // Set to true or function/method to return the image.
        'url'           => false,       // Set to true or function/method to return the url.
    ],
    'json-ld' => [
        'defaults'  => [],             // The default attributes that are always rendered.
        'enabled'   => false,          // Enable or disable JSON-LD globally.
    ],
];

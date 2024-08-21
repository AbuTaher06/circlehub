<?php



    return [
        'default' => env('NOTIFICATION_DRIVER', 'database'),

        'channels' => [
            'mail' => [
                'driver' => 'mail',
            ],
            'database' => [
                'driver' => 'database',
            ],
            // Other channels...
        ],
    ];



<?php

return [
    // You can set the entity who gets subscribed here.
    'model' => App\User::class,

    // Define your callback URI's here
    'redirect' => [
        'success' => null,
        'cancelled' => null,
    ],
    // Change this value to true if you want the Service Provider to create a route for handling Chargebee Webhooks
    'publish_routes' => false
];

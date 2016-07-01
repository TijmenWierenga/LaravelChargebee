<?php

return [
    // You can set the entity who gets subscribed here.
    'model' => App\User::class,
    // Change this value to true if you want the Service Provider to create a route for handling Chargebee Webhooks
    'publish_routes' => false
];

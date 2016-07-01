<?php

Route::post('/chargebee/webhook', \TijmenWierenga\LaravelChargebee\Http\Controllers\WebhookController::class . '@handleWebhook');

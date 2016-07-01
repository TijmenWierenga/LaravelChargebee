<?php
namespace TijmenWierenga\LaravelChargebee\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        return response("We received your webhook but we still need to handle it. Sorry.");
    }
}
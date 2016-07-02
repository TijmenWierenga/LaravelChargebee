<?php
namespace TijmenWierenga\LaravelChargebee\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TijmenWierenga\LaravelChargebee\Subscription;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $webhookEvent = studly_case($request->event_type);

        if (method_exists($this, 'handle' . $webhookEvent)) {
            $payload = json_decode(json_encode($request->input('content')));

            return $this->{'handle' . $webhookEvent}($payload);
        } else {
            return response("No event handler for " . $webhookEvent, 200);
        }
    }

    public function handleSubscriptionCancelled($payload)
    {
        $subscription = (new Subscription)->where('subscription_id', $payload->subscription->id)->first();

        if ($subscription) {
            // TODO: Mark subscription as cancelled instantly.
        }

        return response("Webhook handled successfully.", 200);
    }
}
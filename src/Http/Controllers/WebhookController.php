<?php
namespace TijmenWierenga\LaravelChargebee\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TijmenWierenga\LaravelChargebee\Subscription;

/**
 * Class WebhookController
 * @package TijmenWierenga\LaravelChargebee\Http\Controllers
 */
class WebhookController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
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

    /**
     * @param $payload
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function handleSubscriptionCancelled($payload)
    {
        $subscription = $this->getSubscription($payload->subscription->id);

        if ($subscription) {
            $subscription->updateCancellationDate($payload->subscription->cancelled_at);
        }

        return response("Webhook handled successfully.", 200);
    }

    /**
     * @param $payload
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function handlePaymentSucceeded($payload)
    {
        $subscription = $this->getSubscription($payload->subscription->id);

        if ($subscription) {
            $subscription->ends_at = $payload->subscription->current_term_end;
        }

        return response("Webhook handled successfully.", 200);
    }

    /**
     * @param $subscriptionId
     * @return mixed
     */
    protected function getSubscription($subscriptionId)
    {
        $subscription = (new Subscription)->where('subscription_id', $subscriptionId)->first();

        return $subscription;
    }
}
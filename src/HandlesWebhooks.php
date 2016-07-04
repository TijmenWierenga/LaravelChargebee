<?php
namespace TijmenWierenga\LaravelChargebee;


use Carbon\Carbon;

/**
 * This trait handles webhooks coming from Chargebee
 *
 * Class HandlesWebhooks
 * @package TijmenWierenga\LaravelChargebee
 */
trait HandlesWebhooks
{
    /**
     * Cancel a subscription instantly.
     *
     * Compatible with the following webhooks:
     * subscription_cancelled
     *
     * @return $this
     */
    public function markAsCancelled()
    {
        $this->ends_at = Carbon::now();
        $this->save();

        return $this;
    }
}
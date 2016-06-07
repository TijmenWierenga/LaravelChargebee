<?php
namespace TijmenWierenga\LaravelChargebee;


/**
 * Class Billable
 * @package TijmenWierenga\LaravelChargebee
 */
trait Billable
{
    /**
     * @param null $plan
     * @return Subscriber
     */
    public function subscribe($plan = null)
    {
        return new Subscriber($this, $plan);
    }

    /**
     * @return mixed
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
<?php
namespace TijmenWierenga\LaravelChargebee;

use ChargeBee_Environment;

/**
 * Class Subscriber
 * @package TijmenWierenga\LaravelChargebee
 */
class Subscriber
{
    /**
     * The model who's subscription is created, retrieved, updated or removed.
     *
     * @var null
     */
    private $model;

    /**
     * The Plan ID where the model will subscribe to.
     *
     * @var null
     */
    private $plan = null;

    /**
     *
     */
    public function __construct($model = null, $plan = null)
    {
        // Set up Chargebee environment keys
        ChargeBee_Environment::configure(env('CHARGEBEE_SITE'), env('CHARGEBEE_KEY'));

        // You can set a plan on the constructor, but it's not required
        $this->plan = $plan;
        $this->model = $model;
    }
}
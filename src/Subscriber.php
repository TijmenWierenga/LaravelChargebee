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
     *
     */
    public function __construct()
    {
        ChargeBee_Environment::configure(config('chargebee.site'), config('chargebee.api_key'));
    }
}
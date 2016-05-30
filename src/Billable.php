<?php
namespace TijmenWierenga\LaravelChargebee;


trait Billable
{
    public function subscribe($plan = null)
    {
        return new Subscriber($this, $plan);
    }
}
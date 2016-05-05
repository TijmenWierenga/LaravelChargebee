<?php
namespace TijmenWierenga\LaravelChargebee;


trait Billable
{
    public function subscribe($plan)
    {
        return new Subscriber($this, $plan);
    }
}
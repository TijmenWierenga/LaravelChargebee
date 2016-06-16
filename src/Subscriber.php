<?php
namespace TijmenWierenga\LaravelChargebee;

use ChargeBee_Environment;
use ChargeBee_Subscription;
use Illuminate\Database\Eloquent\Model;
use TijmenWierenga\LaravelChargebee\Exceptions\MissingPlanException;

/**
 * Class Subscriber
 * @package TijmenWierenga\LaravelChargebee
 */
class Subscriber
{
    /**
     * The model who's subscription is created, retrieved, updated or removed.
     *
     * @var Model
     */
    private $model;

    /**
     * The Plan ID where the model will subscribe to.
     *
     * @var null
     */
    private $plan = null;

    /**
     * An array containing all add-ons for the subscription.
     *
     * @var array
     */
    private $addOns = [];

    /**
     * The Coupon ID registeren in Chargebee
     *
     * @var null
     */
    private $coupon = null;

    /**
     * @param Model|null $model
     * @param null $plan
     */
    public function __construct(Model $model = null, $plan = null)
    {
        // Set up Chargebee environment keys
        ChargeBee_Environment::configure(getenv('CHARGEBEE_SITE'), getenv('CHARGEBEE_KEY'));

        // You can set a plan on the constructor, but it's not required
        $this->plan = $plan;
        $this->model = $model;
    }

    /**
     * @param null $cardToken
     * @return array
     * @throws MissingPlanException
     */
    public function create($cardToken = null)
    {
        if (! $this->plan) throw new MissingPlanException('No plan was set to assign to the customer.');

        $subscription = $this->buildSubscription($cardToken);

        $result = ChargeBee_Subscription::create($subscription);
        $subscription = $result->subscription();
        $card = $result->card();
        $addons = $subscription->addons;

        $subscription = $this->model->subscriptions()->create([
            'subscription_id'   => $subscription->id,
            'plan_id'           => $subscription->planId,
            'ends_at'           => $subscription->currentTermEnd,
            'trial_ends_at'     => $subscription->trialEnd,
            'quantity'          => $subscription->planQuantity,
            'last_four'         => ($card) ? $card->last4 : null,
        ]);

        if ($addons) {
            foreach ($addons as $addon)
            {
                $subscription->addons()->create([
                    'quantity' => $addon->quantity,
                    'addon_id' => $addon->id,
                ]);
            }
        }

        return $subscription;
    }

    /**
     * Convenient helper function for adding just one add-on
     *
     * @param $id
     * @param $quantity
     * @return $this
     */
    public function withAddOn($id, $quantity = 1)
    {
        $this->addOns([
            [
                'id' => $id,
                'quantity' => $quantity
            ]
        ]);

        return $this;
    }

    /**
     * Redeem a coupon by adding the coupon ID from Chargebee
     *
     * @param $id
     * @return $this
     */
    public function coupon($id)
    {
        $this->coupon = $id;

        return $this;
    }

    /**
     * Swap an existing subscription
     *
     * @param $subscription
     * @param $plan
     * @return null
     */
    public function swap(Subscription $subscription, $plan)
    {
        $result = ChargeBee_Subscription::update($subscription->subscription_id, [
            'plan_id' => $plan
        ]);

        return $result->subscription();
    }

    /**
     * Adds add-ons to the subscription
     *
     * @param array $addOns
     * @return $this
     */
    public function addOns(array $addOns)
    {
        foreach ($addOns as $addOn)
        {
            // TODO: Check if parameters are valid and catch exception.
            $this->addOns[] = [
                'id'        => $addOn['id'],
                'quantity'  => $addOn['quantity']
            ];
        }

        return $this;
    }

    /**
     * @param $cardToken
     * @return array
     */
    public function buildSubscription($cardToken)
    {
        $subscription = [];
        $subscription['planId'] = $this->plan;
        $subscription['customer'] = [
            'firstName' => $this->model->first_name,
            'lastName'  => $this->model->last_name,
            'email'     => $this->model->email
        ];
        $subscription['addons'] = $this->buildAddOns();
        $subscription['coupon'] = $this->coupon;

        if ($cardToken)
        {
            $subscription['card']['gateway'] = getenv('CHARGEBEE_GATEWAY');
            $subscription['card']['tmpToken'] = $cardToken;
        }

        return $subscription;
    }

    /**
     * @return array|null
     */
    public function buildAddOns()
    {
        if (empty($this->addOns)) return null;

        return $this->addOns;
    }
}
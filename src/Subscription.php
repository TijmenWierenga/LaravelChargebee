<?php
namespace TijmenWierenga\LaravelChargebee;


use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Subscription
 * @package TijmenWierenga\LaravelChargebee
 */
class Subscription extends Model
{
    use HandlesWebhooks;

    protected $fillable = ['subscription_id', 'plan_id', 'user_id', 'quantity', 'last_four', 'trial_ends_at', 'ends_at', 'next_billing_at'];

    /**
     * @var array
     */
    protected $dates = ['ends_at', 'trial_ends_at', 'next_billing_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        $model = env('CHARGEBEE_MODEL') ?: config('chargebee.model', User::class);
        return $this->belongsTo($model, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addons()
    {
        return $this->hasMany(Addon::class);
    }

    /**
     * Change the plan of a subscription
     *
     * @param $plan
     * @return $this
     */
    public function swap($plan)
    {
        $subscriber = new Subscriber();
        $subscriptionDetails = $subscriber->swap($this, $plan);

        $this->plan_id          = $subscriptionDetails->planId;
        $this->trial_ends_at    = $subscriptionDetails->trialEnd;
        $this->next_billing_at  = $subscriptionDetails->currentTermEnd;
        $this->save();

        return $this;
    }

    /**
     * Cancel the subscription
     *
     * @return $this
     */
    public function cancel()
    {
        $subscriber = new Subscriber();
        $subscriptionDetails = $subscriber->cancel($this);

        $this->ends_at = $subscriptionDetails->cancelledAt;
        $this->save();

        return $this;
    }

    /**
     * Cancel a subscription immediately
     *
     * @return $this
     */
    public function cancelImmediately()
    {
        $subscriber = new Subscriber();
        $subscriptionDetails = $subscriber->cancel($this, true);

        $this->ends_at = $subscriptionDetails->cancelledAt;
        $this->trial_ends_at = $subscriptionDetails->cancelledAt;
        $this->save();

        return $this;
    }

    /**
     * Resume a subscription that has a scheduled cancellation.
     *
     * @return $this
     */
    public function resume()
    {
        $subscriber = new Subscriber();
        $subscriptionDetails = $subscriber->resume($this);

        $this->ends_at = null;
        $this->next_billing_at = $subscriptionDetails->currentTermEnd;
        $this->trial_ends_at = $subscriptionDetails->trialEnd;
        $this->save();

        return $this;
    }

    /**
     * Reactivate a cancelled subscription
     *
     * @return $this
     */
    public function reactivate()
    {
        $subscriber = new Subscriber();
        $subscriptionDetails = $subscriber->reactivate($this);

        $this->ends_at = null;
        $this->next_billing_at = $subscriptionDetails->currentTermEnd;
        $this->save();

        return $this;
    }

    /**
     * Check if a subscription is cancelled
     *
     * @return bool
     */
    public function cancelled()
    {
        return (!! $this->ends_at);
    }

    /**
     * Check if a subscription is active
     *
     * @return bool
     */
    public function active()
    {
        if (! $this->valid())
        {
            return $this->onTrial();
        }

        return true;
    }

    /**
     * Check if a subscription is within it's trial period
     *
     * @return bool
     */
    public function onTrial()
    {
        if (!! $this->trial_ends_at)
        {
            return Carbon::now()->lt($this->trial_ends_at);
        }

        return false;
    }

    /**
     * Check if the subscription is not expired
     *
     * @return bool
     */
    public function valid()
    {
        if (! $this->ends_at)
        {
            return true;
        }

        return Carbon::now()->lt($this->ends_at);
    }
}
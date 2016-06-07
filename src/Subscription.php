<?php
namespace TijmenWierenga\LaravelChargebee;


use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Subscription
 * @package TijmenWierenga\LaravelChargebee
 */
class Subscription extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        $model = env('CHARGEBEE_MODEL') ?: config('chargebee.model', User::class);
        return $this->belongsTo($model, 'user_id');
    }
}
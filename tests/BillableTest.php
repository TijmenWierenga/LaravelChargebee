<?php

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Stripe\Token;

class BillableTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        if (file_exists(__DIR__.'/../.env')) {
            $dotenv = new Dotenv\Dotenv(__DIR__.'/../');
            $dotenv->load();
        }
    }

    public function setUp()
    {
        Eloquent::unguard();
        $db = new DB;
        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $db->bootEloquent();
        $db->setAsGlobal();

        $this->schema()->create('users', function($table) {
            $table->increments('id');
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name');
            $table->timestamps();
        });

        $this->schema()->create('subscriptions', function($table) {
            $table->increments('id');
            $table->string('subscription_id');
            $table->string('plan_id');
            $table->string('user_id')->index()->unsigned();
            $table->integer('quantity')->default(1);
            $table->integer('last_four')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('next_billing_at')->nullable();
            $table->timestamps();
        });

        $this->schema()->create('addons', function($table) {
            $table->increments('id');
            $table->integer('subscription_id')->index()->unsigned();
            $table->string('addon_id');
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });
    }

    public function tearDown()
    {
        $this->schema()->drop('users');
        $this->schema()->drop('subscriptions');
        $this->schema()->drop('addons');
    }
    
    /**
    * @test
    */
    public function it_returns_the_subscription_creation_class()
    {
        $user = User::create([
            'email'         => 'tijmen@floown.com',
            'first_name'    => 'Tijmen',
            'last_name'     => 'Wierenga'
        ]);

        $subscriber = $user->subscription('test-plan');

        $this->assertInstanceOf(TijmenWierenga\LaravelChargebee\Subscriber::class, $subscriber);
    }

    /**
    * @test
    */
    public function it_throws_an_exception_when_no_plan_is_provided_when_creating_a_new_subscription()
    {
        $user = User::create([
            'email'         => 'tijmen@floown.com',
            'first_name'    => 'Tijmen',
            'last_name'     => 'Wierenga'
        ]);

        $this->setExpectedException(TijmenWierenga\LaravelChargebee\Exceptions\MissingPlanException::class);

        $user->subscription()->create();
    }

    /**
     * @test
     */
    public function it_registers_a_free_subscription_without_creditcard()
    {
        $user = User::create([
            'email'         => 'tijmen@floown.com',
            'first_name'    => 'Tijmen',
            'last_name'     => 'Wierenga'
        ]);

        $subscription = $user->subscription('cbdemo_free')->create();

        // Test if subscription is created in database
        $this->assertInstanceOf(TijmenWierenga\LaravelChargebee\Subscription::class, $subscription);
        // Test if user has a related subscription
        $this->assertCount(1, $user->subscriptions);
        // Test if subscription has an subscription identifier from Chargebee
        $this->assertNotNull($user->subscriptions->first()->subscription_id);
        // Test if credit card number is null
        $this->assertNull($user->subscriptions->first()->last_four);
    }

    /**
    * @test
    */
    public function it_registers_a_paid_subscription_within_chargebee()
    {
        $user = User::create([
            'email'         => 'tijmen@floown.com',
            'first_name'    => 'Tijmen',
            'last_name'     => 'Wierenga'
        ]);

        $cardToken = $this->getTestToken();

        $subscription = $user->subscription('cbdemo_hustle')->create($cardToken);

        // Test if subscription is created in database
        $this->assertInstanceOf(TijmenWierenga\LaravelChargebee\Subscription::class, $subscription);
        // Test if user has a related subscription
        $this->assertCount(1, $user->subscriptions);
        // Test if subscription has an subscription identifier from Chargebee

        $subscription = $user->subscriptions->first();

        $this->assertNotNull($subscription->subscription_id);
        // Test if credit card number is null
        $this->assertNotNull($subscription->last_four);
        // Test if subscription is on trial
        $this->assertTrue($subscription->onTrial());
        // Test if subscription is active
        $this->assertTrue($subscription->active());
        // Test if subscription is valid
        $this->assertTrue($subscription->valid());

        // Test if subscription can be swapped
        $subscription = $subscription->swap('cbdemo_grow');
        $this->assertEquals('cbdemo_grow', $subscription->plan_id);

        // Test if subscription can be cancelled
        $subscription->cancel();
        // Test if subscription is cancelled
        $subscription->cancelled();
    }

    /**
    * @test
    */
    public function it_registers_a_subscription_with_an_add_on()
    {
        $user = User::create([
            'email'         => 'tijmen@floown.com',
            'first_name'    => 'Tijmen',
            'last_name'     => 'Wierenga'
        ]);

        $cardToken = $this->getTestToken();

        $subscription = $user->subscription('cbdemo_free')
            ->withAddOn('cbdemo_additionaluser')
            ->create($cardToken);

        // Test if add-on was successfully created.
        $this->assertInstanceOf(\TijmenWierenga\LaravelChargebee\Addon::class, $subscription->addons->first());
        // Test if a next billing period is defined
        $this->assertInstanceOf(\Carbon\Carbon::class, $subscription->next_billing_at);
    }

    /**
    * @test
    */
    public function it_registers_a_subscription_with_a_coupon()
    {
        $user = User::create([
            'email'         => 'tijmen@floown.com',
            'first_name'    => 'Tijmen',
            'last_name'     => 'Wierenga'
        ]);

        $cardToken = $this->getTestToken();

        $subscription = $user->subscription('cbdemo_free')
            ->coupon('cbdemo_earlybird')
            ->create($cardToken);

        // Test if subscription has a valid ID.
        $this->assertNotNull($user->subscriptions->first()->subscription_id);
    }

    protected function getTestToken()
    {
        return Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 5,
                'exp_year' => 2020,
                'cvc' => '123',
            ],
        ], ['api_key' => getenv('STRIPE_SECRET')])->id;
    }

    /**
     * Schema Helpers.
     */
    protected function schema()
    {
        return $this->connection()->getSchemaBuilder();
    }
    protected function connection()
    {
        return Eloquent::getConnectionResolver()->connection();
    }
}

class User extends Eloquent {
    use \TijmenWierenga\LaravelChargebee\Billable;
}

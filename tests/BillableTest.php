<?php

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model as Eloquent;

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
            $table->string('plan_id');
            $table->string('user_id');
            $table->integer('quantity');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function tearDown()
    {
        $this->schema()->drop('users');
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

        $subscriber = $user->subscribe('test-plan');

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

        $user->subscribe()->create();
    }

    /**
     * @test
     */
    public function it_registers_a_new_subscription_within_chargebee()
    {
        $user = User::create([
            'email'         => 'tijmen@floown.com',
            'first_name'    => 'Tijmen',
            'last_name'     => 'Wierenga'
        ]);

        $subscription = $user->subscribe('cbdemo_free')->create();

        $this->assertInstanceOf(TijmenWierenga\LaravelChargebee\Subscription::class, $subscription);
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

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
            $table->string('name');
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
    public function it_creates_a_subscription()
    {
    	$user = User::create([
            'email'     => 'tijmen@floown.com',
            'name'      => 'Tijmen Wierenga'
        ]);

        $subscriber = $user->subscribe('test-plan');

        $this->assertInstanceOf(TijmenWierenga\LaravelChargebee\Subscriber::class, $subscriber);
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

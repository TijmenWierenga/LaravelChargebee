# LaravelChargebee

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status](https://travis-ci.org/TijmenWierenga/LaravelChargebee.svg?branch=master)](https://travis-ci.org/TijmenWierenga/LaravelChargebee)
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

A Laravel package which provides an easy way to handle billing and subscriptions.

### WARNING

This package is still under heavy construction. Until further notice, this package will be highly unstable.

## Install

Via Composer

``` bash
$ composer require tijmen-wierenga/laravel-chargebee
```

Next, register the service provider in `config/app.php`:

``` php
['providers'] => [
    TijmenWierenga\LaravelChargebee\ChargebeeServiceProvider::class
]
```

If you want to use the package's routes for handling webhooks, make sure you place the service provider before the Route Service Prodiver (`App\Providers\RouteServiceProvider::class`).

We also need database tables in order to store subscriptions and add-ons. Copy the migrations to your migrations folder by running the following command in your terminal:

``` shell
php artisan vendor:publish
```

Then run the database migrations by entering the following command:

``` shell
php artisan migrate
```

The `vendor:publish` command also publishes a **config** file (`config/chargebee.php`). If you want to use the webhook handler provided by this package, update the `publish_routes` config variable to `true`. 

There are also a few environment variables that need to be added to the `.env`-file:

```
CHARGEBEE_SITE=your-chargebee-site
CHARGEBEE_KEY=your-chargebee-token
CHARGEBEE_MODEL=App\User
```

Also, define your payment gateway details in the `.env`-file:

```
CHARGEBEE_GATEWAY=stripe
```

## Usage

### Creating a new subscription:

Create a new subscription by providing the plan ID to the subscription method and by adding the credit card token to the create method:

``` php
$user->subscription($plan)->create($creditcardToken);
```

You can also add add-ons to a subscription:

``` php
$user->subscription($plan)
    ->withAddon($addon)
    ->create($creditcardToken)
```

Or redeem a coupon:

``` php
$user->subscription($plan)
    ->coupon($coupon)
    ->create($creditcardToken)
```

### Changing plans

``` php
// Get the subscription you want to change plans from
$subscription = $user->subscriptions->first();

// Change the current plan
$subscription->swap($planId);
```

### Cancelling a subscription

``` php
$subscription->cancel();
```


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

If you want to run the unit tests for this package you need acquire test tokens from Stripe. To be able to fetch a test token create an `.env`-file in the base directory and add your stripe secret token:

```
STRIPE_SECRET=your-stripe-secret-key
```

To run the PHPUnit tests, run the following composer command from the base directory:

``` bash
$ composer run test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email tijmen@floown.com instead of using the issue tracker.

## Credits

- [Tijmen Wierenga][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/tijmen-wierenga/laravel-chargebee.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/tijmen-wierenga/laravel-chargebee/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/tijmen-wierenga/laravel-chargebee.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/tijmen-wierenga/laravel-chargebee.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/tijmen-wierenga/laravel-chargebee.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/tijmen-wierenga/laravel-chargebee
[link-travis]: https://travis-ci.org/TijmenWierenga/LaravelChargebee
[link-scrutinizer]: https://scrutinizer-ci.com/g/tijmen-wierenga/laravel-chargebee/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/tijmen-wierenga/laravel-chargebee
[link-downloads]: https://packagist.org/packages/tijmen-wierenga/laravel-chargebee
[link-author]: https://github.com/TijmenWierenga
[link-contributors]: ../../contributors

# LaravelChargebee

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status](https://travis-ci.org/TijmenWierenga/LaravelChargebee.svg?branch=master)](https://travis-ci.org/TijmenWierenga/LaravelChargebee)
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

Next, run the following command in your terminal:

``` bash
php artisan chargebee:install
```

This will copy and run the migrations necessary for the package to work. It also copies the configuration file to your config path.

There are also a few environment variables that need to be added to the `.env`-file:

```
CHARGEBEE_SITE=your-chargebee-site
CHARGEBEE_KEY=your-chargebee-token
```

If you want to use a different payment gateway, define your payment gateway details in the `.env`-file:

```
CHARGEBEE_GATEWAY=stripe
```

## Usage

### Creating a new subscription:

You can create subscriptions in multiple ways:
* Through Chargebee's Hosted Page
* Through Stripe/Braintree's Javascript library

#### Create a subscription with Chargebee's Hosted Page

Retrieve a hosted page URL:

``` php
$url = $user->subscription($planId)->withAddon($addonId)->getCheckoutUrl($embed);
```

The `$embed` variable is a boolean value which describes whether or not you want to embed the hosted page as an i-frame.

You can now choose between redirecting the user to the hosted page, or send it to a view where you can render it:

**Redirect**
``` php
return redirect($url);
```

**Return it as a variable in your view**
``` php
return view('subscribe')->with(compact($url));
```

Next, render it in your view:
``` html
<iframe src="{{ $url }}"></iframe>
```

On success, Chargebee will redirect to their own success page by default, but to register the subscription in our own application, we need to redirect back to our application. To define this redirect, setup a callback route:

```php
    // Define your callback URI's here
    'redirect' => [
        'success' => 'http://chargebee.app/success',
        'cancelled' => null,
    ],
```

Add the route and make a call to the `registerFromHostedPage` method from the controller:
``` php
$user->subscription()->registerFromHostedPage($request->id);
```

The subscription is now registered in both Chargebee and your own application. I coulnd't be easier!

#### Subscriptions with Stripe/Braintree

In order to create subscriptions with Stripe and Braintree, you need to make use of their Javascript libraries. More info on subscribing with Stripe and Braintree can be found below:
* [Stripe](https://www.chargebee.com/docs/stripe.html)
* [Braintree](https://www.chargebee.com/docs/braintree.html)

To create a subscription with Braintree or Stripe you'll have to add a credit card token:

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

## Testing

If you want to run the unit tests for this package you need acquire test tokens from Stripe. To be able to fetch a test token create an `.env`-file in the base directory and add your stripe secret token:

```
STRIPE_SECRET=your-stripe-secret-key
CHARGEBEE_SITE=your-chargebee-site
CHARGEBEE_KEY=your-chargebee-token
CHARGEBEE_GATEWAY=stripe
```

To run the PHPUnit tests, run the following composer command from the base directory:

``` bash
$ composer run test
```

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
[link-downloads]: https://packagist.org/packages/tijmen-wierenga/laravel-chargebee
[link-author]: https://github.com/TijmenWierenga
[link-contributors]: ../../contributors

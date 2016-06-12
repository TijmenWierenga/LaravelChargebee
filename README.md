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

We also need database tables in order to store subscriptions and add-ons. Copy the migrations to your migrations folder by running the following command in your terminal:

``` shell
php artisan vendor:publish
```

Then run the database migrations by entering the following command:

``` shell
php artisan migrate
```

There are also a few environment variables that need to be added to the `.env`-file:

```
CHARGEBEE_SITE=your-chargebee-site
CHARGEBEE_KEY=your-chargebee-token
CHARGEBEE_MODEL=App\User
STRIPE_SECRET=your-stripe-secret-key
CHARGEBEE_GATEWAY=stripe
```

## Usage

### Creating a new subscription:

``` php
$user->subscribe($plan)->create($creditcardToken);
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

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

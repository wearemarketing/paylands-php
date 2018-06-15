# Paylands PHP bindings

[![Build Status](https://travis-ci.org/wearemarketing/paylands-php.svg?branch=master)](https://travis-ci.org/wearemarketing/paylands-php)
[![Latest Stable Version](https://poser.pugx.org/wearemarketing/paylands-php/v/stable.svg)](https://packagist.org/packages/wearemarketing/paylands-php)
[![Total Downloads](https://poser.pugx.org/wearemarketing/paylands-php/downloads.svg)](https://packagist.org/packages/wearemarketing/paylands-php)
[![License](https://poser.pugx.org/wearemarketing/paylands-php/license.svg)](https://packagist.org/packages/wearemarketing/paylands-php)

You can get your Paylands account at [Paynopain](https://http://paylands.com/)

## Requirements

PHP 5.5.0 and later.

## Installation

You can install the library via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require wearemarketing/paylands-php
```

To use the library, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

We use HTTPPlug as the HTTP client abstraction layer, so you must install an HTTP client implementation an its corresponding
adapter. In the followingnexample, we will use a [Guzzle adapter](https://github.com/php-http/guzzle6-adapter) to provide dependencies.

```bash
composer require php-http/guzzle6-adapter
```

If you want to use another HTTP client implementation, you can check [here](https://packagist.org/providers/php-http/client-implementation) the full list of HTTP client implementations. 


## Documentation

Please see [integration tests](./tests/Integration). For up-to-date documentation about Paylands API see [docs](https://paylands.docs.apiary.io/).

## Development

To execute the test suite just follow next steps.

```bash
composer install
```

Install dependencies as mentioned above (which will resolve [PHPUnit](http://packagist.org/packages/phpunit/phpunit)), then you can run the test suite:

```bash
./vendor/bin/phpunit
```

To enable integration tests you must set your API keys in the PHPUnit's config file (see [phpunit.xml.dist](./phpunit.xml.dist)) 

## Community

Find some of the community-supported libraries available for Paylands PHP listed below.

* [PaymentSuite](https://github.com/PaymentSuite/paymentsuite) (Symfony bundle)



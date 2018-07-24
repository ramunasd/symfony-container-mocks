# Symfony Container Mocks

[![Build Status](https://travis-ci.org/ramunasd/symfony-container-mocks.svg?branch=master)](https://travis-ci.org/ramunasd/symfony-container-mocks)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ramunasd/symfony-container-mocks/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ramunasd/symfony-container-mocks/?branch=master)
[![PHP-Eye](https://php-eye.com/badge/ramunasd/symfony-container-mocks/tested.svg?style=flat)](https://php-eye.com/package/ramunasd/symfony-container-mocks)
[![Coverage Status](https://coveralls.io/repos/github/ramunasd/symfony-container-mocks/badge.svg?branch=coveralls)](https://coveralls.io/github/ramunasd/symfony-container-mocks?branch=coveralls)


This container enables you to mock services in the Symfony dependency
injection container. It is particularly useful in functional tests.

## Features

* Can replace any Symfony service or parameter
* Automatically detects service class from service definition
* Can be used with any mocking framework
* Compatible with Symfony versions 2.7 - 3.4
* Works on all supported version of PHP

## OTB supported mocking frameworks

 * [phpspec/prophecy](https://github.com/phpspec/prophecy)

## Installation

Add SymfonyContainerMocks using composer:

`composer require "ramunasd/symfony-container-mocks"`

or edit your composer.json:

```js
{
    "require": {
        "ramunasd/symfony-container-mocks": "*"
    }
}
```


Replace base container class for test environment in `app/AppKernel.php`

```php
<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use RDV\SymfonyContainerMocks\DependencyInjection\TestKernelTrait;

class AppKernel extends Kernel
{
    // use special container when env=test
    use TestKernelTrait;
    
    public function registerBundles()
    {
        return [
            // bundles
        ];
    }
    
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}
```

And clear application cache.


## Examples

### Inject mocked service

```php
<?php

namespace Acme\Bundle\AcmeBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Acme\Bundle\AcmeBundle\Service\Custom;

class AcmeControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client $client
     */
    private $client;

    public function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();
    }

    public function tearDown()
    {
        $this->client->getContainer()->tearDown();
        $this->client = null;

        parent::tearDown();
    }

    public function testSomethingWithMockedService()
    {
        $this->client->getContainer()->prophesize('acme.service.custom', Custom::class)
            ->someMethod([])
            ->willReturn(false)
            ->shouldBeCalledTimes(2);

        // ...
    }
}
```

### Inject automatically mocked service

> feature works only with flag "debug" enabled.
 
```php
$mock = $this->client->getContainer()->prophesize('acme.service.custom');
$mock
    ->myMethod()
    ->willReturn(true);
```

### Other mocking frameworks

```php
// create stub
$mock = $this->getMock(Custom::class);

// inject service mock
self::$kernel->getContainer()->setMock('acme.service.custom', $mock);

// reset container state
self::$kernel->getContainer()->unMock('acme.service.custom');

```

### Set specific framework parameter

```php
// set custom value during test
self::$kernel->getContainer()->setMockedParameter('acme.service.parameter1', 'customValue1');

// trigger service, assert results

// reset all parameters to original values
self::$kernel->getContainer()->clearMockedParameters();
```


## Things TO DO

* Symfony 4.x support
* PSR-11 adoption


## Similar/Related projects

* https://github.com/jakzal/phpunit-injector - inject Symfony services into PHPUnit test cases


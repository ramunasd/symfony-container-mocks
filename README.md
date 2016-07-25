# Symfony Container Mocks

[![Build Status](https://travis-ci.org/ramunasd/symfony-container-mocks.svg?branch=master)](https://travis-ci.org/ramunasd/symfony-container-mocks)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ramunasd/symfony-container-mocks/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ramunasd/symfony-container-mocks/?branch=master)

This container enables you to mock services in the Symfony dependency
injection container. It is particularly useful in functional tests.

## Supported mocking frameworks

 * phpspec/prophecy

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

use RDV\SymfonyContainerMocks\DependencyInjection\TestContainer;

/**
 * @return string
 */
protected function getContainerBaseClass()
{
    if ('test' == $this->environment) {
        return TestContainer::class;
    }
    
    return parent::getContainerBaseClass();
}
```

And clear application cache.


## Features

### Simple mocking

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
    private $client = null;

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

### Class name autodetection 

This feature works only with flag "debug" enabled.
 
```php
    
    $mock = $this->client->getContainer()->prophesize('acme.service.custom');
    $mock
        ->myMethod()
        ->willReturn(true);
    
```

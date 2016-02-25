# Symfony Container Mocks

[![Build Status](https://travis-ci.org/ramunasd/symfony-container-mocks.svg?branch=master)](https://travis-ci.org/ramunasd/symfony-container-mocks)

This container enables you to mock services in the Symfony dependency
injection container. It is particularly useful in functional tests.

## Supported mocking frameworks

 * phpspec/prophecy

## Installation

Add SymfonyContainerMocks to your composer.json:

```js
{
    "require": {
        "ramuansd/symfony-container-mocks": "*"
    }
}
```

Replace base container class for test environment in `app/AppKernel.php`

```php
<?php

/**
 * @return string
 */
protected function getContainerBaseClass()
{
    if ('test' == $this->environment) {
        return '\RDV\SymfonyContainerMocks\DependencyInjection\ContainerMocks';
    }
    
    return parent::getContainerBaseClass();
}
```

And clear your cache.


## Features

### Simple mocking

```php
<?php

namespace Acme\Bundle\AcmeBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
        $this->client->getContainer()->prophesize('acme.service.custom', 'Acme\Bundle\AcmeBundle\Service\Custom')
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

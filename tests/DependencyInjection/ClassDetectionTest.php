<?php

namespace RDV\Tests\SymfonyContainerMocks\DependencyInjection;

use RDV\SymfonyContainerMocks\DependencyInjection\ContainerMocks;
use RDV\SymfonyContainerMocks\DependencyInjection\DefinitionLoader;
use RDV\Tests\SymfonyContainerMocks\Fixtures\TestKernel;

class ClassDetectionTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        array_map('unlink', glob(__DIR__ . '/../Fixtures/cache/test/*.[php|xml]*'));
        DefinitionLoader::unload();
    }

    public function testServiceClassIsDetectedCorrectly()
    {
        $kernel = $this->getKernel();
        /** @var ContainerMocks $container */
        $container = $kernel->getContainer();
        $mock = $container->prophesize('test_service');
        $this->assertInstanceOf('Prophecy\Prophecy\ObjectProphecy', $mock);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testExceptionIsThrownWhenDebugIsDisabled()
    {
        $kernel = $this->getKernel(false);
        $kernel->getContainer()->prophesize('test_service');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionIsThrownOnUnknownService()
    {
        $kernel = $this->getKernel();
        $kernel->getContainer()->prophesize('test_service2');
    }

    /**
     * @param bool $debug
     * @return TestKernel
     */
    protected function getKernel($debug = true)
    {
        include_once __DIR__ . '/../Fixtures/TestKernel.php';
        $kernel = new TestKernel('test', $debug);
        $kernel->boot();

        return $kernel;
    }
}

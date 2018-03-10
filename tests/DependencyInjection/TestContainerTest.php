<?php

namespace RDV\Tests\SymfonyContainerMocks\DependencyInjection;

use RDV\SymfonyContainerMocks\DependencyInjection\TestContainer;

class TestContainerTest extends \PHPUnit_Framework_TestCase
{
    const KERNEL_CLASS = TestContainer::class;

    /**
     * @var TestContainer $container
     */
    private $container;

    /**
     * @var array $services
     */
    private $services = array();

    protected function setUp()
    {
        $class = self::KERNEL_CLASS;
        $this->container = new $class;

        foreach (array('service1', 'service2', 'service3') as $id) {
            $service = new \stdClass();
            $service->id = $id;

            $this->services[$id] = $service;
            $this->container->set($id, $service);
        }
    }

    protected function tearDown()
    {
        $this->container->tearDown();
    }

    public function testCustomMockSetClear()
    {
        $id = mt_rand();
        $service = $this->createMock('Symfony\Component\Config\Loader\LoaderInterface');
        $service
            ->expects($this->once())
            ->method('load')
            ->willReturn(true);
        $this->container->setMock($id, $service);

        // simulate call on mock
        $this->assertTrue($this->container->get($id)->load('test'));

        // clear container state
        $this->container->unMock($id);

        // must fail cause service does not exist
        try {
            $this->container->get($id);
        } catch (\Exception $e) {
            $this->assertInstanceOf('Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException', $e);
        }
    }

    public function testBehaviorDoesNotChangeByDefault()
    {
        $this->assertTrue($this->container->has('service1'));
        $this->assertTrue($this->container->has('service2'));
        $this->assertTrue($this->container->has('service3'));
        $this->assertSame($this->services['service1'], $this->container->get('service1'));
        $this->assertSame($this->services['service2'], $this->container->get('service2'));
        $this->assertSame($this->services['service3'], $this->container->get('service3'));
    }

    public function testExistingServiceCanBeMocked()
    {
        $id = 'service1';
        $mock = $this->container->prophesize($id, 'stdClass');

        $this->assertInstanceOf('Prophecy\Prophecy\ObjectProphecy', $mock);
        $this->assertTrue($this->container->has($id));
        $this->assertNotSame($this->services[$id], $this->container->get($id));
        $this->assertSame($mock->reveal(), $this->container->get($id));
    }

    public function testNonExistingServiceCanBeMocked()
    {
        $mock = $this->container->prophesize('serviceX', 'stdClass');
        $this->assertInstanceOf('Prophecy\Prophecy\ObjectProphecy', $mock);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage This service already mocked and can have references
     */
    public function testServiceCannotBeMockedTwice()
    {
        $id = 'service1';
        $this->container->prophesize($id, 'stdClass');
        $this->container->prophesize($id, 'stdClass');
    }

    public function testMockedServicesAreAccessible()
    {
        $mock1 = $this->container->prophesize('service1', 'stdClass');
        $mock2 = $this->container->prophesize('service2', 'stdClass');

        $mockedServices = $this->container->getMockedServices();

        $this->assertEquals(array('service1' => $mock1->reveal(), 'service2' => $mock2->reveal()), $mockedServices);
    }

    public function testMockCanBeRemovedAndContainerFallsBackToTheOriginalService()
    {
        $id = 'service1';
        $this->container->prophesize($id, 'stdClass');
        $this->container->unMock($id);

        $this->assertTrue($this->container->has($id));
        $this->assertEquals($this->services[$id], $this->container->get($id));
    }

    public function testContainerResetClearsMockedService()
    {
        $this->container->prophesize('service1', 'stdClass');
        $this->assertNotEmpty($this->container->getMockedServices());
        $this->container->reset();
        $this->assertEmpty($this->container->getMockedServices());
    }

    public function testMockedServiceMustBeInitialized()
    {
        $id = 'service1';
        $this->assertTrue($this->container->initialized($id));

        $this->container->prophesize($id, 'stdClass');
        $this->assertTrue($this->container->initialized($id));

        $id = 'serviceX';
        $this->container->prophesize($id, 'stdClass');
        $this->assertTrue($this->container->initialized($id));
    }
}


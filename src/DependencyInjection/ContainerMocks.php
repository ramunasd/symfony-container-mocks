<?php

namespace RDV\SymfonyContainerMocks\DependencyInjection;

use Prophecy\Prophet;
use Symfony\Component\DependencyInjection\Container;

class ContainerMocks extends Container
{
    /**
     * @var array
     */
    protected $mocked = array();

    /**
     * @var Prophet
     */
    protected $prophet;

    /**
     * @param string $id The service identifier
     * @param string $class Class or interface fully qualified name
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    public function prophesize($id, $class)
    {
        if (array_key_exists($id, $this->mocked)) {
            throw new \InvalidArgumentException('This service already mocked and can have references');
        }

        $mock = $this->getProphet()->prophesize($class);
        $this->mocked[$id] = $mock->reveal();

        return $mock;
    }

    /**
     * Remove all mocked services
     */
    public function tearDown()
    {
        $this->mocked = array();
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        parent::reset();
        $this->mocked = array();
    }

    /**
     * @param string $id
     */
    public function unMock($id)
    {
        unset($this->mocked[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        if (array_key_exists($id, $this->mocked)) {
            return $this->mocked[$id];
        }

        return parent::get($id, $invalidBehavior);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        if (array_key_exists($id, $this->mocked)) {
            return true;
        }

        return parent::has($id);
    }

    /**
     * {@inheritdoc}
     */
    public function initialized($id)
    {
        if (array_key_exists($id, $this->mocked)) {
            return true;
        }

        return parent::initialized($id);
    }

    /**
     * @return array
     */
    public function getMockedServices()
    {
        return $this->mocked;
    }

    /**
     * @return Prophet
     */
    protected function getProphet()
    {
        if (!$this->prophet) {
            $this->prophet = new Prophet();
        }

        return $this->prophet;
    }
}

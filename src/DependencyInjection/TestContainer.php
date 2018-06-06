<?php

namespace RDV\SymfonyContainerMocks\DependencyInjection;

use Prophecy\Prophet;
use Symfony\Component\DependencyInjection\Container;

class TestContainer extends Container
{
    /**
     * @var array
     */
    private $mocked = [];

    /**
     * @var array|null
     */
    private $parametersOriginal;

    /**
     * @var \ReflectionProperty
     */
    private $parametersReflection;

    /**
     * @var Prophet
     */
    protected $prophet;

    /**
     * @param string      $id The service identifier
     * @param string|null $class Class or interface fully qualified name
     * @return \Prophecy\Prophecy\ObjectProphecy
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     * @throws \Prophecy\Exception\Prophecy\ObjectProphecyException
     */
    public function prophesize($id, $class = null)
    {
        if (array_key_exists($id, $this->mocked)) {
            throw new \InvalidArgumentException('This service already mocked and can have references');
        }

        if (empty($class)) {
            $class = $this->detectClass($id);
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
        $this->mocked = [];
        $this->clearMockedParameters();
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        if (interface_exists('Symfony\Component\DependencyInjection\ResettableContainerInterface')
            && $this instanceof \Symfony\Component\DependencyInjection\ResettableContainerInterface) {
            parent::reset();
        }
        $this->tearDown();
    }

    /**
     * @param string $id
     * @param mixed  $mock
     */
    public function setMock($id, $mock)
    {
        $this->mocked[$id] = $mock;
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
    public function getProphet()
    {
        if (!$this->prophet) {
            $this->prophet = new Prophet();
        }

        return $this->prophet;
    }

    /**
     * @param string $service
     * @return string
     * @throws \BadMethodCallException
     */
    protected function detectClass($service)
    {
        return DefinitionLoader::getClassName($service, $this);
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @throws \ReflectionException
     */
    public function setMockedParameter($name, $value)
    {
        $reflection = $this->getParametersReflection();
        $parameters = $reflection->getValue($this);
        if (!$this->parametersOriginal) {
            $this->parametersOriginal = $parameters;
        }
        $parameters[$name] = $value;
        $reflection->setValue($this, $parameters);
    }

    /**
     * @throws \ReflectionException
     */
    public function clearMockedParameters()
    {
        if ($this->parametersOriginal) {
            $reflection = $this->getParametersReflection();
            $reflection->setValue($this, $this->parametersOriginal);
            $this->parametersOriginal = null;
        }
    }

    /**
     * @return \ReflectionProperty
     * @throws \ReflectionException
     */
    private function getParametersReflection()
    {
        if (!$this->parametersReflection) {
            $this->parametersReflection = new \ReflectionProperty($this, 'parameters');
            $this->parametersReflection->setAccessible(true);
        }

        return $this->parametersReflection;
    }
}


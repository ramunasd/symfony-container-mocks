<?php

namespace RDV\Tests\SymfonyContainerMocks\Fixtures;

use RDV\SymfonyContainerMocks\DependencyInjection\TestContainerTrait;
use RDV\SymfonyContainerMocks\DependencyInjection\TestKernelTrait;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Tests\Fixtures\KernelForTest;

class TestKernel extends KernelForTest
{
    use TestKernelTrait;

    public function registerBundles()
    {
        return array(
            new FrameworkBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config.php');
    }

    protected function prepareContainer(ContainerBuilder $container)
    {
        $container->register('test_service', 'stdClass');

        parent::prepareContainer($container);
    }
}

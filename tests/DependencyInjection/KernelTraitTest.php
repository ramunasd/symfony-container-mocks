<?php

namespace RDV\Tests\SymfonyContainerMocks\DependencyInjection;

use RDV\SymfonyContainerMocks\DependencyInjection\TestKernelTrait;

include __DIR__ . '/TestContainerTest.php';

class KernelTraitTest extends TestContainerTest
{
    const CONTAINER_CLASS = TestKernelTrait::class;
}

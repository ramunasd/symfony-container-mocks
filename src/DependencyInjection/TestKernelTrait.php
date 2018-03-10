<?php

namespace RDV\SymfonyContainerMocks\DependencyInjection;

trait TestKernelTrait
{
    /**
     * @return string
     */
    protected function getContainerBaseClass()
    {
        if ('test' === $this->environment) {
            return TestContainer::class;
        }

        return parent::getContainerBaseClass();
    }
}


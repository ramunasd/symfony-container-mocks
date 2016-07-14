<?php

namespace RDV\SymfonyContainerMocks\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;

class DefinitionLoader
{
    /** @var array */
    protected static $definitions = [];

    /**
     * @param Container $container
     */
    protected static function loadDefinitions(Container $container)
    {
        if (empty(self::$definitions)) {
            if (!$container->hasParameter('debug.container.dump')) {
                throw new \BadMethodCallException('Class autodetection works only with "debug" enabled');
            }

            $dump = $container->getParameter('debug.container.dump');
            $xml = simplexml_load_file($dump);

            foreach ($xml->services->service as $service) {
                $attributes = $service->attributes();
                $id = (string)$attributes['id'];
                $class = (string)$attributes['class'];
                if (!empty($class)) {
                    self::$definitions[$id] = $class;
                }
            }
        }
    }

    /**
     * Unload all cached definitions
     */
    public static function unload()
    {
        self::$definitions = [];
    }

    /**
     * @param string $service
     * @param Container $container
     * @return string mixed
     */
    public static function getClassName($service, Container $container)
    {
        self::loadDefinitions($container);

        if (empty(self::$definitions[$service])) {
            throw new \InvalidArgumentException(
                sprintf('Service "%s" is not defined or class name cannot be detected', $service)
            );
        }

        return self::$definitions[$service];
    }
}


<?php

namespace BsbFlysystem\Adapter\Factory;

use League\Flysystem\Adapter\NullAdapter as Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NullAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->mergeMvcConfig($serviceLocator, func_get_arg(2));

        $this->validateConfig();

        $adapter = new Adapter();

        return $adapter;
    }

    /**
     * @inheritdoc
     */
    protected function validateConfig()
    {
    }
}

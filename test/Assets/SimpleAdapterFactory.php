<?php

namespace BsbFlysystemTest\Assets;

use BsbFlysystem\Adapter\Factory\AbstractAdapterFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SimpleAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
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
    }

    /**
     * @inheritdoc
     */
    public function validateConfig()
    {

    }
}

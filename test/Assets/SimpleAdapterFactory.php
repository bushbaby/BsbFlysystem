<?php

declare(strict_types=1);

namespace BsbFlysystemTest\Assets;

use BsbFlysystem\Adapter\Factory\AbstractAdapterFactory;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\AdapterInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SimpleAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
{
    public function doCreateService(ServiceLocatorInterface $serviceLocator): AdapterInterface
    {
        $this->mergeMvcConfig($serviceLocator, func_get_arg(2));

        $this->validateConfig();

        return new NullAdapter();
    }

    public function validateConfig()
    {
    }
}

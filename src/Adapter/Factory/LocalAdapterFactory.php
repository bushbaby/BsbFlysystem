<?php

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\Adapter\Local as Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LocalAdapterFactory extends AbstractAdapterFactory
{
    /**
     * @inheritdoc
     */
    public function doCreateService(ServiceLocatorInterface $serviceLocator)
    {
        $adapter = new Adapter($this->options['root']);

        return $adapter;
    }

    /**
     * @inheritdoc
     */
    protected function validateConfig()
    {
        if (!isset($this->options['root'])) {
            throw new UnexpectedValueException("Missing 'root' as option");
        }
    }
}

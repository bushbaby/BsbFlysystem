<?php

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\AdapterInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LocalAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ServiceLocatorInterface $serviceLocator): AdapterInterface
    {
        $adapter = new Adapter($this->options['root']);

        return $adapter;
    }

    protected function validateConfig()
    {
        if (! isset($this->options['root'])) {
            throw new UnexpectedValueException("Missing 'root' as option");
        }
    }
}

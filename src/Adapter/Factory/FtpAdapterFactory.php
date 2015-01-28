<?php

namespace BsbFlysystem\Adapter\Factory;

use League\Flysystem\Adapter\Ftp as Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FtpAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
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

        $adapter = new Adapter($this->options);

        return $adapter;
    }

    /**
     * @inheritdoc
     */
    protected function validateConfig()
    {
        if (!isset($this->options['host'])) {
            throw new \UnexpectedValueException("Missing 'host' as option");
        }

        if (!isset($this->options['port'])) {
            throw new \UnexpectedValueException("Missing 'port' as option");
        }

        if (!isset($this->options['username'])) {
            throw new \UnexpectedValueException("Missing 'username' as option");
        }

        if (!isset($this->options['password'])) {
            throw new \UnexpectedValueException("Missing 'password' as option");
        }
    }
}

<?php

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\Replicate\ReplicateAdapter as Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReplicateAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Adapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->mergeMvcConfig($serviceLocator, func_get_arg(2));

        $this->validateConfig();

        if (!class_exists('League\Flysystem\Replicate\ReplicateAdapter')) {
            throw new RequirementsException(
                ['league/flysystem-replicate-adapter'],
                'Replicate'
            );
        }

        while (is_callable([$serviceLocator, 'getServiceLocator'])) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        $connectionManager = $serviceLocator->get('BsbFlysystemAdapterManager');

        $adapter = new Adapter(
            $connectionManager->get($this->options['source']),
            $connectionManager->get($this->options['replicate'])
        );

        return $adapter;
    }

    /**
     * @inheritdoc
     */
    protected function validateConfig()
    {
        if (!isset($this->options['source'])) {
            throw new \UnexpectedValueException("Missing 'source' as option");
        }

        if (!isset($this->options['replicate'])) {
            throw new \UnexpectedValueException("Missing 'replicate' as option");
        }
    }
}

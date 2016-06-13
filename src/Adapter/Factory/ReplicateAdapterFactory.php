<?php

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\Replicate\ReplicateAdapter as Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReplicateAdapterFactory extends AbstractAdapterFactory
{
    /**
     * @inheritdoc
     */
    public function doCreateService(ServiceLocatorInterface $serviceLocator)
    {
        if (!class_exists(\League\Flysystem\Replicate\ReplicateAdapter::class)) {
            throw new RequirementsException(
                ['league/flysystem-replicate-adapter'],
                'Replicate'
            );
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
            throw new UnexpectedValueException("Missing 'source' as option");
        }

        if (!isset($this->options['replicate'])) {
            throw new UnexpectedValueException("Missing 'replicate' as option");
        }
    }
}

<?php

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\Replicate\ReplicateAdapter as Adapter;
use UnexpectedValueException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReplicateAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function doCreateService(ServiceLocatorInterface $serviceLocator)
    {
        if (!class_exists('League\Flysystem\Replicate\ReplicateAdapter')) {
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

<?php

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Replicate\ReplicateAdapter as Adapter;
use Psr\Container\ContainerInterface;

class ReplicateAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        if (! class_exists(\League\Flysystem\Replicate\ReplicateAdapter::class)) {
            throw new RequirementsException(
                ['league/flysystem-replicate-adapter'],
                'Replicate'
            );
        }

        $connectionManager = $container->get('BsbFlysystemAdapterManager');

        $adapter = new Adapter(
            $connectionManager->get($this->options['source']),
            $connectionManager->get($this->options['replicate'])
        );

        return $adapter;
    }

    protected function validateConfig()
    {
        if (! isset($this->options['source'])) {
            throw new UnexpectedValueException("Missing 'source' as option");
        }

        if (! isset($this->options['replicate'])) {
            throw new UnexpectedValueException("Missing 'replicate' as option");
        }
    }
}

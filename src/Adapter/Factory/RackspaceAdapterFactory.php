<?php

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Rackspace\RackspaceAdapter as Adapter;
use OpenCloud\OpenStack;
use Psr\Container\ContainerInterface;

class RackspaceAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        if (! class_exists(\League\Flysystem\Rackspace\RackspaceAdapter::class) ||
            ! class_exists(\ProxyManager\Factory\LazyLoadingValueHolderFactory::class)
        ) {
            throw new RequirementsException(
                ['league/flysystem-rackspace', 'ocramius/proxy-manager'],
                'Rackspace'
            );
        }

        /** @var AdapterInterface $proxy */
        $proxy = $this->getLazyFactory($container)->createProxy(
            \League\Flysystem\Rackspace\RackspaceAdapter::class,
            function (&$wrappedObject, $proxy, $method, $parameters, &$initializer) {
                $client = new OpenStack(
                    $this->options['url'],
                    $this->options['secret'],
                    $this->options['options']
                );

                $store = $client->objectStoreService(
                    $this->options['objectstore']['name'],
                    $this->options['objectstore']['region'],
                    $this->options['objectstore']['url_type']
                );

                $container = $store->getContainer($this->options['objectstore']['container']);

                $wrappedObject = new Adapter($container, $this->options['prefix']);

                return true;
            }
        );

        return $proxy;
    }

    protected function validateConfig()
    {
        if (! isset($this->options['url'])) {
            throw new UnexpectedValueException("Missing 'url' as option");
        }

        if (! isset($this->options['secret']) || ! is_array($this->options['secret'])) {
            throw new UnexpectedValueException("Missing 'secret' as option");
        }

        if (! isset($this->options['objectstore']) || ! is_array($this->options['objectstore'])) {
            throw new UnexpectedValueException("Missing 'objectstore' as option");
        } elseif (! isset($this->options['objectstore']['name'])) {
            throw new UnexpectedValueException("Missing 'objectstore.name' as option");
        } elseif (! isset($this->options['objectstore']['region'])) {
            throw new UnexpectedValueException("Missing 'objectstore.region' as option");
        } elseif (! isset($this->options['objectstore']['container'])) {
            throw new UnexpectedValueException("Missing 'objectstore.container' as option");
        }

        if (! isset($this->options['objectstore']['url_type'])) {
            $this->options['objectstore']['url_type'] = null;
        }

        if (! isset($this->options['options'])) {
            $this->options['options'] = [];
        }

        if (! isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

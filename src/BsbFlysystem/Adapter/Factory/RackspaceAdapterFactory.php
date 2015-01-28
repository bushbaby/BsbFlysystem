<?php

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\Rackspace\RackspaceAdapter as Adapter;
use OpenCloud\OpenStack;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RackspaceAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
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

        if (!class_exists('League\Flysystem\Rackspace\RackspaceAdapter') ||
            !class_exists('ProxyManager\Factory\LazyLoadingValueHolderFactory')
        ) {
            throw new RequirementsException(
                sprintf(
                    "Install '%s' to use '%s'",
                    implode(', ', ['league/flysystem-rackspace', 'ocramius/proxy-manager']),
                    'League\Flysystem\Rackspace\RackspaceAdapter'
                )
            );
        }

        $proxy = $this->getLazyFactory($serviceLocator)->createProxy(
            'League\Flysystem\Rackspace\RackspaceAdapter',
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

    /**
     * @inheritdoc
     */
    protected function validateConfig()
    {
        if (!isset($this->options['url'])) {
            throw new \UnexpectedValueException("Missing 'url' as option");
        }

        if (!isset($this->options['secret']) || !is_array($this->options['secret'])) {
            throw new \UnexpectedValueException("Missing 'secret' as option");
        }

        if (!isset($this->options['objectstore']) || !is_array($this->options['objectstore'])) {
            throw new \UnexpectedValueException("Missing 'objectstore' as option");
        } elseif (!isset($this->options['objectstore']['name'])) {
            throw new \UnexpectedValueException("Missing 'objectstore.name' as option");
        } elseif (!isset($this->options['objectstore']['region'])) {
            throw new \UnexpectedValueException("Missing 'objectstore.region' as option");
        } elseif (!isset($this->options['objectstore']['container'])) {
            throw new \UnexpectedValueException("Missing 'objectstore.container' as option");
        }

        if (!isset($this->options['objectstore']['url_type'])) {
            $this->options['objectstore']['url_type'] = null;
        }

        if (!isset($this->options['options'])) {
            $this->options['options'] = [];
        }

        if (!isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

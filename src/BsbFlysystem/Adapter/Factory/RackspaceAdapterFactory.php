<?php

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\Adapter\Rackspace as Adapter;
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

        if (!class_exists('OpenCloud\OpenStack') ||
            !class_exists('ProxyManager\Factory\LazyLoadingValueHolderFactory')
        ) {
            throw new RequirementsException(
                sprintf(
                    "Install '%s' to use '%s'",
                    implode(', ', array('rackspace/php-opencloud', 'ocramius/proxy-manager')),
                    'League\Flysystem\Adapter\Rackspace'
                )
            );
        }

        $self = $this;

        $proxy = $this->getLazyFactory($serviceLocator)->createProxy(
            'League\Flysystem\Adapter\Rackspace',
            function (&$wrappedObject, $proxy, $method, $parameters, &$initializer) use ($self) {
                $client = new OpenStack(
                    $self->options['url'],
                    $self->options['secret'],
                    $self->options['options']
                );

                $store = $client->objectStoreService(
                    $self->options['objectstore']['name'],
                    $self->options['objectstore']['region'],
                    $self->options['objectstore']['url_type']
                );

                $container = $store->getContainer($self->options['objectstore']['container']);

                $wrappedObject = new Adapter($container, $self->options['prefix']);

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
            $this->options['options'] = array();
        }

        if (!isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

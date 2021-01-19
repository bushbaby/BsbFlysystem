<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2021 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Rackspace\RackspaceAdapter as Adapter;
use OpenCloud\OpenStack;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Psr\Container\ContainerInterface;

class RackspaceAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        if (! \class_exists(Adapter::class) ||
            ! \class_exists(LazyLoadingValueHolderFactory::class)
        ) {
            throw new RequirementsException(['league/flysystem-rackspace', 'ocramius/proxy-manager'], 'Rackspace');
        }

        /** @var AdapterInterface $proxy */
        $proxy = $this->getLazyFactory($container)->createProxy(
            Adapter::class,
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

    protected function validateConfig(): void
    {
        if (! isset($this->options['url'])) {
            throw new UnexpectedValueException("Missing 'url' as option");
        }

        if (! isset($this->options['secret']) || ! \is_array($this->options['secret'])) {
            throw new UnexpectedValueException("Missing 'secret' as option");
        }

        if (! isset($this->options['objectstore']) || ! \is_array($this->options['objectstore'])) {
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

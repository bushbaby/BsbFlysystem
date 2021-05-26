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
use League\Flysystem\Replicate\ReplicateAdapter as Adapter;
use Psr\Container\ContainerInterface;

class ReplicateAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        if (! \class_exists(Adapter::class)) {
            throw new RequirementsException(['league/flysystem-replicate-adapter'], 'Replicate');
        }

        $connectionManager = $container->get('BsbFlysystemAdapterManager');

        return new Adapter(
            $connectionManager->get($this->options['source']),
            $connectionManager->get($this->options['replicate'])
        );
    }

    protected function validateConfig(): void
    {
        if (! isset($this->options['source'])) {
            throw new UnexpectedValueException("Missing 'source' as option");
        }

        if (! isset($this->options['replicate'])) {
            throw new UnexpectedValueException("Missing 'replicate' as option");
        }
    }
}

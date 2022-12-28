<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use Ajgl\Flysystem\Replicate\ReplicateFilesystemAdapter;
use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Service\AdapterManager;
use League\Flysystem\FilesystemAdapter;
use Psr\Container\ContainerInterface;

class ReplicateAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): FilesystemAdapter
    {
        if (! class_exists(ReplicateFilesystemAdapter::class)) {
            throw new RequirementsException(['ajgl/flysystem-replicate'], 'Replicate');
        }

        $manager = $container->get(AdapterManager::class);

        return new ReplicateFilesystemAdapter(
            $manager->get($this->options['source']),
            $manager->get($this->options['replica'])
        );
    }

    protected function validateConfig(): void
    {
        \assert(
            \array_key_exists('source', $this->options),
            "Required option 'source' is missing"
        );

        \assert(
            \is_string($this->options['source']) && ! empty($this->options['source']),
            "Option 'source' must be a non empty string"
        );

        \assert(
            \array_key_exists('replica', $this->options),
            "Required option 'replica' is missing"
        );

        \assert(
            \is_string($this->options['replica']) && ! empty($this->options['replica']),
            "Option 'replica' must be a non empty string"
        );
    }
}

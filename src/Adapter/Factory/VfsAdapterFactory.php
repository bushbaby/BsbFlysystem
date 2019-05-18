<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2019 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Vfs\VfsAdapter as Adapter;
use Psr\Container\ContainerInterface;
use VirtualFileSystem\FileSystem as Vfs;

class VfsAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        if (! \class_exists(Adapter::class)) {
            throw new RequirementsException(
                ['league/flysystem-vfs'],
                'Vfs'
            );
        }

        return new Adapter(new Vfs());
    }

    /**
     * This adapter has no options.
     */
    protected function validateConfig(): void
    {
    }
}

<?php

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
        if (! class_exists(\League\Flysystem\Vfs\VfsAdapter::class)) {
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
    protected function validateConfig()
    {
    }
}

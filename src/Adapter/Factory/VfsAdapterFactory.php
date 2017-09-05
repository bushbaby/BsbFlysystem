<?php

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Vfs\VfsAdapter as Adapter;
use VirtualFileSystem\FileSystem as Vfs;
use Zend\ServiceManager\ServiceLocatorInterface;

class VfsAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ServiceLocatorInterface $serviceLocator): AdapterInterface
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

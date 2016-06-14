<?php

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\Vfs\VfsAdapter as Adapter;
use VirtualFileSystem\FileSystem as Vfs;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class VfsAdapterFactory extends AbstractAdapterFactory
{
    /**
     * @inheritdoc
     */
    public function doCreateService(ServiceLocatorInterface $serviceLocator)
    {
        if (!class_exists(\League\Flysystem\Vfs\VfsAdapter::class)) {
            throw new RequirementsException(
                ['league/flysystem-vfs'],
                'Vfs'
            );
        }

        return new Adapter(new Vfs());
    }

    /**
     * @inheritdoc
     *
     * This adapter has no options
     */
    protected function validateConfig()
    {

    }
}

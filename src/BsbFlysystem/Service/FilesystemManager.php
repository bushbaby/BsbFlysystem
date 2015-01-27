<?php

namespace BsbFlysystem\Service;

use League\Flysystem\FilesystemInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

class FilesystemManager extends AbstractPluginManager
{
    /**
     * {@inheritDoc}
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof FilesystemInterface) {
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Filesystem of type %s is invalid; must implement \League\Flysystem\FilesystemInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}

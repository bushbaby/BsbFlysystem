<?php

namespace BsbFlysystem\Service;

use League\Flysystem\Filesystem;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

class FilesystemManager extends AbstractPluginManager
{
    /**
     * {@inheritDoc}
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Filesystem) {
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Filesystem of type %s is invalid; must implement \League\Flysystem\Filesystem',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}

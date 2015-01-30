<?php

namespace BsbFlysystem\Service;

use League\Flysystem\FilesystemInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Exception;

class FilesystemManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->addAbstractFactory('BsbFlysystem\Filesystem\Factory\FilesystemAbstractFactory');
    }

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

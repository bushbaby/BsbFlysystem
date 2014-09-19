<?php

namespace BsbFlysystem\Service;

use League\Flysystem\AdapterInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

class AdapterManager extends AbstractPluginManager
{
    /**
     * {@inheritDoc}
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof AdapterInterface) {
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Adapter of type %s is invalid; must implement \League\Flysystem\Adapter\AbstractAdapter',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}

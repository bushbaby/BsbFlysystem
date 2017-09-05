<?php

declare(strict_types=1);

namespace BsbFlysystem\Service;

use BsbFlysystem\Exception\RuntimeException;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

class FilesystemManager extends AbstractPluginManager
{
    /**
     * {@inheritdoc}
     */
    protected $instanceOf = \League\Flysystem\FilesystemInterface::class;

    /**
     * {@inheritdoc}
     */
    protected $shareByDefault = true;

    /**
     * {@inheritdoc}
     */
    protected $sharedByDefault = true;

    /**
     * {@inheritdoc}
     */
    public function validate($instance)
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new Exception\InvalidServiceException(sprintf(
                'Invalid filesystem "%s" created; not an instance of %s',
                get_class($instance),
                $this->instanceOf
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validatePlugin($instance)
    {
        try {
            $this->validate($instance);
        } catch (Exception\InvalidServiceException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

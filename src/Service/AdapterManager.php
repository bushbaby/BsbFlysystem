<?php

declare(strict_types=1);

namespace BsbFlysystem\Service;

use BsbFlysystem\Exception\RuntimeException;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

class AdapterManager extends AbstractPluginManager
{
    /**
     * {@inheritdoc}
     */
    protected $instanceOf = \League\Flysystem\AdapterInterface::class;

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
    protected $factories = [
        'League\Flysystem\Adapter\NullAdapter' => \Zend\ServiceManager\Factory\InvokableFactory::class,
        'leagueflysystemadapternulladapter'    => \Zend\ServiceManager\Factory\InvokableFactory::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function validate($instance)
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new Exception\InvalidServiceException(sprintf(
                'Invalid adapter "%s" created; not an instance of %s',
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

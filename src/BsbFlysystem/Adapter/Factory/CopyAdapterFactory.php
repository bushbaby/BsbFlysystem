<?php

namespace BsbFlysystem\Adapter\Factory;

use Barracuda\Copy\API;
use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\Adapter\Copy as Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CopyAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->mergeMvcConfig($serviceLocator, func_get_arg(2));

        $this->validateConfig();

        if (!class_exists('Barracuda\Copy\API')) {
            throw new RequirementsException(
                sprintf(
                    "Install '%s' to use '%s'",
                    implode(', ', ['barracuda/copy']),
                    'League\Flysystem\Adapter\Copy'
                )
            );
        }

        $client = new API(
            $this->options['consumer_key'],
            $this->options['consumer_secret'],
            $this->options['access_token'],
            $this->options['token_secret']
        );

        $adapter = new Adapter($client, $this->options['prefix']);

        return $adapter;
    }

    /**
     * @inheritdoc
     */
    protected function validateConfig()
    {
        if (!isset($this->options['consumer_key'])) {
            throw new \UnexpectedValueException("Missing 'consumer_key' as option");
        }

        if (!isset($this->options['consumer_secret'])) {
            throw new \UnexpectedValueException("Missing 'consumer_secret' as option");
        }

        if (!isset($this->options['access_token'])) {
            throw new \UnexpectedValueException("Missing 'access_token' as option");
        }

        if (!isset($this->options['token_secret'])) {
            throw new \UnexpectedValueException("Missing 'token_secret' as option");
        }

        if (!isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

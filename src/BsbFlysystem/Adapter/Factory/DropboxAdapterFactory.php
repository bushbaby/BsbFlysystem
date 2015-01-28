<?php

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\Dropbox\DropboxAdapter as Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DropboxAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
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

        if (!class_exists('League\Flysystem\Dropbox\DropboxAdapter')) {
            throw new RequirementsException(
                sprintf(
                    "Install '%s' to use '%s'",
                    'league/flysystem-dropbox',
                    'League\Flysystem\Dropbox\DropboxAdapter'
                )
            );
        }

        $client = new \Dropbox\Client(
            $this->options['access_token'],
            $this->options['client_identifier'],
            $this->options['user_locale']
        );

        $adapter = new Adapter($client, $this->options['prefix']);

        return $adapter;
    }

    /**
     * @inheritdoc
     */
    protected function validateConfig()
    {
        if (!isset($this->options['access_token'])) {
            throw new \UnexpectedValueException("Missing 'access_token' as option");
        }

        if (!isset($this->options['client_identifier'])) {
            throw new \UnexpectedValueException("Missing 'client_identifier' as option");
        }

        if (!isset($this->options['user_locale'])) {
            $this->options['user_locale'] = null;
        }

        if (!isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

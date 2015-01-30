<?php

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\Dropbox\DropboxAdapter as Adapter;
use UnexpectedValueException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DropboxAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function doCreateService(ServiceLocatorInterface $serviceLocator)
    {
        if (!class_exists('League\Flysystem\Dropbox\DropboxAdapter')) {
            throw new RequirementsException(
                ['league/flysystem-dropbox'],
                'Dropbox'
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
            throw new UnexpectedValueException("Missing 'access_token' as option");
        }

        if (!isset($this->options['client_identifier'])) {
            throw new UnexpectedValueException("Missing 'client_identifier' as option");
        }

        if (!isset($this->options['user_locale'])) {
            $this->options['user_locale'] = null;
        }

        if (!isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

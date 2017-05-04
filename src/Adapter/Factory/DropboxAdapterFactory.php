<?php

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use Spatie\FlysystemDropbox\DropboxAdapter as Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DropboxAdapterFactory extends AbstractAdapterFactory
{
    /**
     * @inheritdoc
     */
    public function doCreateService(ServiceLocatorInterface $serviceLocator)
    {
        if (!class_exists(\Spatie\FlysystemDropbox\DropboxAdapter::class)) {
            throw new RequirementsException(
                ['spatie/flysystem-dropbox'],
                'Dropbox'
            );
        }

        $client = new \Spatie\Dropbox\Client(
            $this->options['authorization_token']
        );

        $adapter = new Adapter($client, $this->options['prefix']);

        return $adapter;
    }

    /**
     * @inheritdoc
     */
    protected function validateConfig()
    {
        if (isset($this->options['access_token']) || isset($this->options['client_identifier'])) {
            throw new UnexpectedValueException("Options 'access_token' and 'client_identifier' should be replaced with an 'authorization_token' for the dropbox adapter");
        }

        if (!isset($this->options['authorization_token'])) {
            throw new UnexpectedValueException("Missing 'authorization_token' as option");
        }

        if (!isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

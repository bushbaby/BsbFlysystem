<?php

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\AdapterInterface;
use Spatie\FlysystemDropbox\DropboxAdapter as Adapter;
use Zend\ServiceManager\ServiceLocatorInterface;

class DropboxAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ServiceLocatorInterface $serviceLocator): AdapterInterface
    {
        if (! class_exists(\Spatie\FlysystemDropbox\DropboxAdapter::class)) {
            throw new RequirementsException(
                ['spatie/flysystem-dropbox'],
                'Dropbox'
            );
        }

        $client = new \Spatie\Dropbox\Client(
            $this->options['access_token']
        );

        $adapter = new Adapter($client, $this->options['prefix']);

        return $adapter;
    }

    protected function validateConfig()
    {
        if (! isset($this->options['access_token'])) {
            throw new UnexpectedValueException("Missing 'access_token' as option");
        }

        if (! isset($this->options['prefix'])) {
            $this->options['prefix'] = '';
        }
    }
}

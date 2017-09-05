<?php

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use Spatie\FlysystemDropbox\DropboxAdapter as Adapter;
use Zend\ServiceManager\ServiceLocatorInterface;

class DropboxAdapterFactory extends AbstractAdapterFactory
{
    /**
     * {@inheritdoc}
     */
    public function doCreateService(ServiceLocatorInterface $serviceLocator)
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

    /**
     * {@inheritdoc}
     */
    protected function validateConfig()
    {
        if (! isset($this->options['access_token'])) {
            throw new UnexpectedValueException("Missing 'access_token' as option");
        }

        if (! isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

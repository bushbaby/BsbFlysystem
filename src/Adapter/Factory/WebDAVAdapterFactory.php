<?php

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\WebDAV\WebDAVAdapter as Adapter;
use Sabre\DAV\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WebDAVAdapterFactory extends AbstractAdapterFactory
{
    /**
     * @inheritdoc
     */
    public function doCreateService(ServiceLocatorInterface $serviceLocator)
    {
        if (!class_exists(\League\Flysystem\WebDAV\WebDAVAdapter::class)) {
            throw new RequirementsException(
                ['league/flysystem-webdav'],
                'WebDAV'
            );
        }

        $client = new Client($this->options);

        $adapter = new Adapter($client, $this->options['prefix']);

        return $adapter;
    }

    /**
     * @inheritdoc
     */
    protected function validateConfig()
    {
        if (!isset($this->options['baseUri'])) {
            throw new UnexpectedValueException("Missing 'baseUri' as option");
        }

        if (!isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

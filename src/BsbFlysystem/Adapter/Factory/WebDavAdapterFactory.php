<?php

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\WebDAV\WebDAVAdapter as Adapter;
use Sabre\DAV\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WebDavAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
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

        if (!class_exists('Sabre\DAV\Client')) {
            throw new RequirementsException(
                sprintf(
                    "Install '%s' to use '%s'",
                    implode(', ', ['sabre/dav']),
                    'League\Flysystem\WebDAV\WebDAVAdapter'
                )
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
            throw new \UnexpectedValueException("Missing 'baseUri' as option");
        }

        if (!isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

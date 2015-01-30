<?php

namespace BsbFlysystem\Adapter\Factory;

use Barracuda\Copy\API;
use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\Copy\CopyAdapter as Adapter;
use UnexpectedValueException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CopyAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function doCreateService(ServiceLocatorInterface $serviceLocator)
    {
        if (!class_exists('League\Flysystem\Copy\CopyAdapter')) {
            throw new RequirementsException(
                ['league/flysystem-copy'],
                'Copy'
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
            throw new UnexpectedValueException("Missing 'consumer_key' as option");
        }

        if (!isset($this->options['consumer_secret'])) {
            throw new UnexpectedValueException("Missing 'consumer_secret' as option");
        }

        if (!isset($this->options['access_token'])) {
            throw new UnexpectedValueException("Missing 'access_token' as option");
        }

        if (!isset($this->options['token_secret'])) {
            throw new UnexpectedValueException("Missing 'token_secret' as option");
        }

        if (!isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

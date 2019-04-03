<?php

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Sftp\SftpAdapter as Adapter;
use Psr\Container\ContainerInterface;

class SftpAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        if (! class_exists(\League\Flysystem\Sftp\SftpAdapter::class)) {
            throw new RequirementsException(
                ['league/flysystem-sftp'],
                'Sftp'
            );
        }

        $adapter = new Adapter($this->options);

        return $adapter;
    }

    protected function validateConfig()
    {
        if (! isset($this->options['host'])) {
            throw new UnexpectedValueException("Missing 'host' as option");
        }

        if (! isset($this->options['port'])) {
            throw new UnexpectedValueException("Missing 'port' as option");
        }

        if (! isset($this->options['username'])) {
            throw new UnexpectedValueException("Missing 'username' as option");
        }

        if (! isset($this->options['password']) && ! isset($this->options['privateKey'])) {
            throw new UnexpectedValueException("Missing either 'password' or 'privateKey' as option");
        }
    }
}

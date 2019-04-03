<?php

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\Adapter\Ftp as Adapter;
use League\Flysystem\AdapterInterface;
use Psr\Container\ContainerInterface;

class FtpAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
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

        if (! isset($this->options['password'])) {
            throw new UnexpectedValueException("Missing 'password' as option");
        }
    }
}

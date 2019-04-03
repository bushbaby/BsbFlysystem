<?php

declare(strict_types=1);

namespace BsbFlysystemTest\Assets;

use BsbFlysystem\Adapter\Factory\AbstractAdapterFactory;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\AdapterInterface;
use Psr\Container\ContainerInterface;

class SimpleAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        $this->mergeMvcConfig($container, func_get_arg(2));

        $this->validateConfig();

        return new NullAdapter();
    }

    public function validateConfig()
    {
    }
}

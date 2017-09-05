<?php

declare(strict_types=1);

namespace BsbFlysystem\Service\Factory;

use BsbFlysystem\Filesystem\Factory\FilesystemFactory;
use BsbFlysystem\Service\FilesystemManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FilesystemManagerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator): FilesystemManager
    {
        return $this($serviceLocator, null);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FilesystemManager
    {
        $config        = $container->get('config');
        $config        = $config['bsb_flysystem']['filesystems'];
        $serviceConfig = [];
        foreach ($config as $key => $filesystems) {
            $serviceConfig['factories'][$key] = FilesystemFactory::class;
            $serviceConfig['shared'][$key]    = isset($filesystems['shared']) ? (bool) $filesystems['shared'] : true;
        }

        return new FilesystemManager($container, $serviceConfig);
    }
}

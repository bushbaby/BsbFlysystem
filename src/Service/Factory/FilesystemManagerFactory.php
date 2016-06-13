<?php

namespace BsbFlysystem\Service\Factory;

use BsbFlysystem\Filesystem\Factory\FilesystemFactory;
use BsbFlysystem\Service\FilesystemManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FilesystemManagerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, null);
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
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

<?php

namespace BsbFlysystem\Service\Factory;

use BsbFlysystem\Service\FilesystemManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FilesystemManagerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        while (is_callable([$serviceLocator, 'getServiceLocator'])) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        $config        = $serviceLocator->get('config');
        $config        = $config['bsb_flysystem'];
        $serviceConfig = $config['filesystem_manager']['services'];

        foreach ($config['filesystems'] as $name => $filesystemConfig) {
            if (isset($filesystemConfig['shared'])) {
                $serviceConfig['shared'][$name] = filter_var($filesystemConfig['shared'], FILTER_VALIDATE_BOOLEAN);
            }

            $serviceConfig['factories'][$name] = 'BsbFlysystem\Filesystem\Factory\FilesystemFactory';
        }

        $serviceConfig = new Config($serviceConfig);

        return new FilesystemManager($serviceConfig);
    }
}

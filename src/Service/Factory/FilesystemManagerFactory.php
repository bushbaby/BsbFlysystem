<?php

namespace BsbFlysystem\Service\Factory;

use BsbFlysystem\Service\FilesystemManager;
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
        $filsystemManager = new FilesystemManager;
        $filsystemManager->setServiceLocator($serviceLocator);

        return $filsystemManager;
    }
}

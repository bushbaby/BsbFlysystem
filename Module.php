<?php

namespace BsbFlysystem;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array('Zend\Loader\StandardAutoloader' => array('namespaces' => array(
            __NAMESPACE__ => __DIR__ . '/src/BsbFlysystem',
        )));
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

}

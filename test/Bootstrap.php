<?php

namespace BsbFlysystemTest;

use RuntimeException;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

/**
 * Test bootstrap
 */
class Bootstrap
{
    /**
     * @var ServiceManager
     */
    protected static $serviceManager;

    public static function init()
    {
        error_reporting(E_ALL | E_STRICT);
        chdir(__DIR__);

        static::initAutoloader();

        $serviceManager = new ServiceManager();
        $config = self::getApplicationConfig();
        $serviceManagerConfig = new ServiceManagerConfig($config);
        $serviceManagerConfig->configureServiceManager($serviceManager);
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        static::$serviceManager = $serviceManager;
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (is_readable($vendorPath . '/autoload.php')) {
            $loader = include $vendorPath . '/autoload.php';

            return;
        }

        throw new RuntimeException(
            'Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.'
        );
    }

    public static function getApplicationConfig()
    {
        $config = [];
        if (!$config = @include __DIR__ . '/TestConfiguration.php') {
            $config = require __DIR__ . '/TestConfiguration.php.dist';
        }

        return $config;
    }

    public static function chroot()
    {
        $rootPath = dirname(static::findParentPath('vendor'));
        chdir($rootPath);
    }

    /**
     * @return ServiceManager
     */
    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    protected static function findParentPath($path)
    {
        $dir         = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }

        return $dir . '/' . $path;
    }
}

Bootstrap::init();
Bootstrap::chroot();

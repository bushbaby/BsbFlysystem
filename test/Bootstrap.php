<?php

namespace BsbFlysystemTest;

use RuntimeException;
use Zend\Loader\AutoloaderFactory;
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

        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', self::getApplicationConfig());
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

        $zf2Path = getenv('ZF2_PATH') ?: (defined('ZF2_PATH') ? ZF2_PATH :
            (is_dir($vendorPath . '/ZF2/library') ? $vendorPath . '/ZF2/library' : false));

        if (!$zf2Path) {
            throw new RuntimeException(
                'Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.'
            );
        }

        if (isset($loader)) {
            $loader->add('Zend', $zf2Path . '/Zend');
        } else {
            include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
            AutoloaderFactory::factory([
                'Zend\Loader\StandardAutoloader' => [
                    'autoregister_zf' => true,
                    'namespaces'      => [
                        'BsbFlysystem' => __DIR__ . '/../src/',
                        __NAMESPACE__  => __DIR__,
                    ],
                ],
            ]);
        }
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

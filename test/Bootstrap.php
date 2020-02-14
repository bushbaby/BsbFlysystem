<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2019 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystemTest;

use Psr\Container\ContainerInterface;
use RuntimeException;
use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\ServiceManager;

class Bootstrap
{
    /**
     * @var ServiceManager
     */
    protected static $serviceManager;

    public static function init(): void
    {
        \error_reporting(E_ALL | E_STRICT);
        \chdir(__DIR__);

        static::initAutoloader();

        $serviceManager = new ServiceManager();
        $config = self::getApplicationConfig();
        $serviceManagerConfig = new ServiceManagerConfig($config);
        $serviceManagerConfig->configureServiceManager($serviceManager);
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        static::$serviceManager = $serviceManager;
    }

    protected static function initAutoloader(): void
    {
        $vendorPath = static::findParentPath('vendor');

        if (\is_readable($vendorPath . '/autoload.php')) {
            $loader = include $vendorPath . '/autoload.php';

            return;
        }

        throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
    }

    public static function getApplicationConfig(): array
    {
        $config = [];
        if (! $config = @include __DIR__ . '/TestConfiguration.php') {
            $config = require __DIR__ . '/TestConfiguration.php.dist';
        }

        return $config;
    }

    public static function chroot(): void
    {
        $rootPath = \dirname(static::findParentPath('vendor'));
        \chdir($rootPath);
    }

    public static function getServiceManager(): ContainerInterface
    {
        return static::$serviceManager;
    }

    protected static function findParentPath($path): ?string
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (! \is_dir($dir . '/' . $path)) {
            $dir = \dirname($dir);
            if ($previousDir === $dir) {
                return null;
            }
            $previousDir = $dir;
        }

        return $dir . '/' . $path;
    }
}

Bootstrap::init();
Bootstrap::chroot();

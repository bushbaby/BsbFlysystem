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

namespace BsbFlysystemTest\Service;

use BsbFlysystem\Service\FilesystemManager;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Filesystem;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceManager;

class FilesystemManagerTest extends TestCase
{
    public function testCreateViaServiceManager()
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get(\BsbFlysystem\Service\FilesystemManager::class);

        $this->assertInstanceOf(\BsbFlysystem\Service\FilesystemManager::class, $manager);
    }

    public function testCreateByAliasViaServiceManager()
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemManager');

        $this->assertInstanceOf(\BsbFlysystem\Service\FilesystemManager::class, $manager);
    }

    public function testManagerValidatesPlugin()
    {
        $manager = new FilesystemManager(new ServiceManager());
        $plugin = $this->getMockBuilder(\League\Flysystem\FilesystemInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertNull($manager->validatePlugin($plugin));

        $this->expectException('RuntimeException');

        $plugin = new \stdClass();
        $this->assertNull($manager->validatePlugin($plugin));
    }

    public function testCanGetSpecificFilesystem()
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemManager');

        $this->assertInstanceOf(\League\Flysystem\FilesystemInterface::class, $manager->get('default'));
    }

    public function testServicesSharedByDefault()
    {
        $sm = Bootstrap::getServiceManager();
        /** @var AbstractPluginManager $manager */
        $manager = $sm->get(\BsbFlysystem\Service\FilesystemManager::class);

        $localA = $manager->get('default');
        $localB = $manager->get('default');
        $this->assertTrue($localA === $localB);
    }

    public function testConfigurationOverrideableForNotSharedServices()
    {
        $sm = Bootstrap::getServiceManager();
        /** @var FilesystemManager $manager */
        $manager = $sm->get(\BsbFlysystem\Service\FilesystemManager::class);

        /** @var Filesystem $filesystem */
        $filesystem = $manager->get('default_unshared');

        /** @var AbstractAdapter $adapter */
        $adapter = $filesystem->getAdapter();

        $pathPrefix = $adapter->getPathPrefix();
        $pathPrefix = \str_replace(\realpath('.'), '', $pathPrefix);

        $this->assertEquals('./test/_build/files/', $pathPrefix);

        /** @var Filesystem $filesystem */
        $filesystem = $manager->get('default_unshared',
            ['adapter_options' => ['root' => './test/_build/documents']]);

        /** @var AbstractAdapter $adapter */
        $adapter = $filesystem->getAdapter();

        $pathPrefix = $adapter->getPathPrefix();
        $pathPrefix = \str_replace(\realpath('.'), '', $pathPrefix);

        $this->assertEquals('./test/_build/documents/', $pathPrefix);
    }

    public function testCanGetCachedFilesystem()
    {
        $sm = Bootstrap::getServiceManager();

        /** @var FilesystemManager $manager */
        $manager = $sm->get(\BsbFlysystem\Service\FilesystemManager::class);

        /** @var Filesystem $filesystem */
        $filesystem = $manager->get('default_cached');

        /** @var CachedAdapter $adapter */
        $adapter = $filesystem->getAdapter();

        $this->assertInstanceOf(\League\Flysystem\Cached\CachedAdapter::class, $adapter);
    }
}

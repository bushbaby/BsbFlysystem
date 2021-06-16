<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2021 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystemTest\Service;

use BsbFlysystem\Service\FilesystemManager;
use BsbFlysystemTest\Bootstrap;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ServiceManager;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\TestCase;
use stdClass;

class FilesystemManagerTest extends TestCase
{
    public function testCreateViaServiceManager(): void
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get(FilesystemManager::class);

        $this->assertInstanceOf(FilesystemManager::class, $manager);
    }

    public function testCreateByAliasViaServiceManager(): void
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemManager');

        $this->assertInstanceOf(FilesystemManager::class, $manager);
    }

    public function testManagerValidatesPlugin(): void
    {
        $manager = new FilesystemManager(new ServiceManager());
        $plugin = $this->getMockBuilder(FilesystemInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $manager->validatePlugin($plugin);

        $this->expectException('RuntimeException');

        $plugin = new stdClass();
        $manager->validatePlugin($plugin);
    }

    public function testCanGetSpecificFilesystem(): void
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemManager');

        $this->assertInstanceOf(FilesystemOperator::class, $manager->get('default'));
    }

    public function testServicesSharedByDefault(): void
    {
        $sm = Bootstrap::getServiceManager();
        /** @var AbstractPluginManager $manager */
        $manager = $sm->get(FilesystemManager::class);

        $localA = $manager->get('default');
        $localB = $manager->get('default');
        $this->assertTrue($localA === $localB);
    }

    public function testConfigurationOverrideableForNotSharedServices(): void
    {
        $sm = Bootstrap::getServiceManager();
        /** @var FilesystemManager $manager */
        $manager = $sm->get(FilesystemManager::class);

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
}

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

namespace BsbFlysystemTest\Filesystem\Factory;

use BsbFlysystem\Cache\ZendStorageCache;
use BsbFlysystem\Exception\UnexpectedValueException;
use BsbFlysystem\Filesystem\Factory\FilesystemFactory;
use Interop\Container\ContainerInterface;
use Laminas\Cache\Storage\StorageInterface;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\CacheInterface;
use League\Flysystem\EventableFilesystem\EventableFilesystem;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class FilesystemFactoryTest extends TestCase
{
    public function testThrowsExceptionForMissingAdapter(): void
    {
        $factory = new FilesystemFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => [],
                ],
            ],
        ];

        $serviceLocatorMock = $this->createMock(ContainerInterface::class);
        $serviceLocatorMock->expects($this->exactly(1))->method('get')->with('config')->willReturn($config);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage("Missing 'adapter' key for the filesystem 'named_fs' configuration");

        $factory($serviceLocatorMock, 'named_fs');
    }

    public function testCreateServiceWithNameReturnsFilesystem(): void
    {
        $factory = new FilesystemFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => [
                        'adapter' => 'named_adapter',
                    ],
                ],
            ],
        ];

        $adapter = new NullAdapter();
        $adapterPluginMock = $this->createMock(ContainerInterface::class);

        $serviceLocatorMock = $this->createMock(ContainerInterface::class);
        $serviceLocatorMock->method('get')->willReturnMap([
            ['config', $config],
            ['BsbFlysystemAdapterManager', $adapterPluginMock],
        ]);

        $adapterPluginMock->method('get')->with('named_adapter')->willReturn($adapter);

        $service = $factory($serviceLocatorMock, 'named_fs');

        $this->assertInstanceOf(FilesystemInterface::class, $service);
        $this->assertInstanceOf(Filesystem::class, $service);
        $this->assertNotInstanceOf(EventableFilesystem::class, $service);
    }

    public function testCreateServiceWithNameReturnsEventableFilesystem(): void
    {
        $factory = new FilesystemFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => [
                        'adapter' => 'named_adapter',
                        'eventable' => true,
                    ],
                ],
            ],
        ];

        $adapter = new NullAdapter();
        $adapterPluginMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $serviceLocatorMock = $this->createMock(ContainerInterface::class);
        $serviceLocatorMock->method('get')->willReturnMap([
            ['config', $config],
            ['BsbFlysystemAdapterManager', $adapterPluginMock],
        ]);

        $adapterPluginMock->method('get')->with('named_adapter')->willReturn($adapter);

        $service = $factory($serviceLocatorMock, 'named_fs');

        $this->assertInstanceOf(FilesystemInterface::class, $service);
        $this->assertInstanceOf(EventableFilesystem::class, $service);
    }

    public function testCreateServiceWithNameCachedAdapter(): void
    {
        $factory = new FilesystemFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => [
                        'adapter' => 'named_adapter',
                        'cache' => 'named/cache',
                    ],
                ],
            ],
        ];

        $adapter = new NullAdapter();
        $adapterPluginMock = $this->createMock(ContainerInterface::class);
        $adapterPluginMock->method('get')->with('named_adapter')->willReturn($adapter);
        $cacheMock = $this->createMock(CacheInterface::class);
        $serviceLocatorMock = $this->createMock(ContainerInterface::class);
        $serviceLocatorMock->method('get')->willReturnMap([
            ['config', $config],
            ['BsbFlysystemAdapterManager', $adapterPluginMock],
            ['named/cache', $cacheMock],
        ]);

        /** @var Filesystem $service */
        $service = $factory($serviceLocatorMock, 'named_fs');

        $this->assertInstanceOf(CachedAdapter::class, $service->getAdapter());
    }

    public function testCreateServiceWithNameCachedAdapterLaminasCacheStorage(): void
    {
        $factory = new FilesystemFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => [
                        'adapter' => 'named_adapter',
                        'cache' => 'named/cache',
                    ],
                ],
            ],
        ];

        $adapter = new NullAdapter();
        $adapterPluginMock = $this->createMock(ContainerInterface::class);
        $adapterPluginMock->method('get')->with('named_adapter')->willReturn($adapter);
        $serviceLocatorMock = $this->createMock(ContainerInterface::class);
        $cacheMock = $this->createMock(StorageInterface::class);

        $serviceLocatorMock->method('get')->willReturnMap([
            ['config', $config],
            ['BsbFlysystemAdapterManager', $adapterPluginMock],
            ['named_adapter', $adapter],
            ['named/cache', $cacheMock],
        ]);

        /** @var Filesystem $service */
        $service = $factory($serviceLocatorMock, 'named_fs');

        $this->assertInstanceOf(CachedAdapter::class, $service->getAdapter());

        $class = new ReflectionClass(CachedAdapter::class);
        $property = $class->getProperty('cache');
        $property->setAccessible(true);

        $cacheInstance = $property->getValue($service->getAdapter());
        $this->assertInstanceOf(ZendStorageCache::class, $cacheInstance);
    }

    public function testCreateServiceWithNameReturnsFilesystemWithPluginsAdded(): void
    {
        $factory = new FilesystemFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => [
                        'adapter' => 'named_adapter',
                        'plugins' => [
                            'League\Flysystem\Plugin\ListPaths',
                        ],
                    ],
                ],
            ],
        ];

        $adapter = new NullAdapter();
        $adapterPluginMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);
        $serviceLocatorMock = $this->createMock(ContainerInterface::class);
        $serviceLocatorMock->method('get')->willReturnMap([
            ['config', $config],
            ['BsbFlysystemAdapterManager', $adapterPluginMock],
        ]);

        $service = $factory($serviceLocatorMock, 'named_fs');

        // works because plugin is registered
        $this->assertEmpty($service->listPaths());
    }
}

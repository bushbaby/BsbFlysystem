<?php

declare(strict_types=1);

namespace BsbFlysystemTest\Filesystem\Factory;

use BsbFlysystem\Filesystem\Factory\FilesystemFactory;
use BsbFlysystemTest\Framework\TestCase;
use Interop\Container\ContainerInterface;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;

class FilesystemFactoryTest extends TestCase
{
    public function testThrowsExceptionForMissingAdapter()
    {
        $factory = new FilesystemFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => [],
                ],
            ],
        ];

        $serviceLocatorMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $this->expectException(\BsbFlysystem\Exception\UnexpectedValueException::class);
        $this->expectExceptionMessage("Missing 'adapter' key for the filesystem 'named_fs' configuration");

        $factory($serviceLocatorMock, 'named_fs');
    }

    public function testCreateServiceWithNameReturnsFilesystem()
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

        $serviceLocatorMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter = new NullAdapter();
        $adapterPluginMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $service = $factory($serviceLocatorMock, 'named_fs');

        $this->assertInstanceOf(\League\Flysystem\FilesystemInterface::class, $service);
        $this->assertInstanceOf(\League\Flysystem\Filesystem::class, $service);
        $this->assertNotInstanceOf(\League\Flysystem\EventableFilesystem\EventableFilesystem::class, $service);
    }

    public function testCreateServiceWithNameReturnsEventableFilesystem()
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

        $serviceLocatorMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter = new NullAdapter();
        $adapterPluginMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $service = $factory($serviceLocatorMock, 'named_fs');

        $this->assertInstanceOf(\League\Flysystem\FilesystemInterface::class, $service);
        $this->assertInstanceOf(\League\Flysystem\EventableFilesystem\EventableFilesystem::class, $service);
    }

    public function testCreateServiceWithNameCachedAdapter()
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

        $serviceLocatorMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter = new NullAdapter();
        $adapterPluginMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $cacheMock = $this->getMockBuilder(\League\Flysystem\Cached\CacheInterface::class)->getMock();
        $serviceLocatorMock->expects($this->at(2))->method('get')->with('named/cache')->willReturn($cacheMock);

        /** @var Filesystem $service */
        $service = $factory($serviceLocatorMock, 'named_fs');

        $this->assertInstanceOf(\League\Flysystem\Cached\CachedAdapter::class, $service->getAdapter());
    }

    public function testCreateServiceWithNameCachedAdapterZendCacheStorage()
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

        $serviceLocatorMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter = new NullAdapter();
        $adapterPluginMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $cacheMock = $this->getMockBuilder('Zend\Cache\Storage\StorageInterface')->getMock();
        $serviceLocatorMock->expects($this->at(2))->method('get')->with('named/cache')->willReturn($cacheMock);

        /** @var Filesystem $service */
        $service = $factory($serviceLocatorMock, 'named_fs');

        $this->assertInstanceOf(\League\Flysystem\Cached\CachedAdapter::class, $service->getAdapter());

        $class = new \ReflectionClass(\League\Flysystem\Cached\CachedAdapter::class);
        $property = $class->getProperty('cache');
        $property->setAccessible(true);

        $cacheInstance = $property->getValue($service->getAdapter());
        $this->assertInstanceOf(\BsbFlysystem\Cache\ZendStorageCache::class, $cacheInstance);
    }

    public function testCreateServiceWithNameReturnsFilesystemWithPluginsAdded()
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

        $serviceLocatorMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter = new NullAdapter();
        $adapterPluginMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $service = $factory($serviceLocatorMock, 'named_fs');

        // works because plugin is registered
        $this->assertEmpty($service->listPaths());
    }
}

<?php

namespace BsbFlysystemTest\Filesystem\Factory;

use BsbFlysystem\Filesystem\Factory\FilesystemFactory;
use BsbFlysystemTest\Framework\TestCase;
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
                    'named_fs' => []
                ]
            ]
        ];

        $serviceLocatorMock       = $this->getMock('Interop\Container\ContainerInterface');
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $this->setExpectedException(
            'BsbFlysystem\Exception\UnexpectedValueException',
            "Missing 'adapter' key for the filesystem 'named_fs' configuration"
        );

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
                    ]
                ],
            ]
        ];

        $serviceLocatorMock       = $this->getMock('Interop\Container\ContainerInterface');
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter           = new NullAdapter();
        $adapterPluginMock = $this->getMock('Interop\Container\ContainerInterface');
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $service = $factory($serviceLocatorMock, 'named_fs');

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $service);
        $this->assertInstanceOf('League\Flysystem\Filesystem', $service);
        $this->assertNotInstanceOf('League\Flysystem\EventableFilesystem\EventableFilesystem', $service);
    }

    public function testCreateServiceWithNameReturnsEventableFilesystem()
    {
        $factory = new FilesystemFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => [
                        'adapter'   => 'named_adapter',
                        'eventable' => true
                    ]
                ],
            ]
        ];

        $serviceLocatorMock       = $this->getMock('Interop\Container\ContainerInterface');
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter           = new NullAdapter();
        $adapterPluginMock = $this->getMock('Interop\Container\ContainerInterface');
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $service = $factory($serviceLocatorMock, 'named_fs');

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $service);
        $this->assertInstanceOf('League\Flysystem\EventableFilesystem\EventableFilesystem', $service);
    }

    public function testCreateServiceWithNameCachedAdapter()
    {
        $factory = new FilesystemFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => [
                        'adapter' => 'named_adapter',
                        'cache'   => 'named/cache'
                    ]
                ],
            ]
        ];

        $serviceLocatorMock       = $this->getMock('Interop\Container\ContainerInterface');
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter           = new NullAdapter();
        $adapterPluginMock = $this->getMock('Interop\Container\ContainerInterface');
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $cacheMock = $this->getMock('League\Flysystem\Cached\CacheInterface');
        $serviceLocatorMock->expects($this->at(2))->method('get')->with('named/cache')->willReturn($cacheMock);

        /** @var Filesystem $service */
        $service = $factory($serviceLocatorMock, 'named_fs');

        $this->assertInstanceOf('League\Flysystem\Cached\CachedAdapter', $service->getAdapter());
    }

    public function testCreateServiceWithNameCachedAdapterZendCacheStorage()
    {
        $factory = new FilesystemFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => [
                        'adapter' => 'named_adapter',
                        'cache'   => 'named/cache'
                    ]
                ],
            ]
        ];

        $serviceLocatorMock       = $this->getMock('Interop\Container\ContainerInterface');
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter           = new NullAdapter();
        $adapterPluginMock = $this->getMock('Interop\Container\ContainerInterface');
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $cacheMock = $this->getMock('Zend\Cache\Storage\StorageInterface');
        $serviceLocatorMock->expects($this->at(2))->method('get')->with('named/cache')->willReturn($cacheMock);

        /** @var Filesystem $service */
        $service = $factory($serviceLocatorMock, 'named_fs');

        $this->assertInstanceOf('League\Flysystem\Cached\CachedAdapter', $service->getAdapter());

        $class    = new \ReflectionClass('League\Flysystem\Cached\CachedAdapter');
        $property = $class->getProperty("cache");
        $property->setAccessible(true);

        $cacheInstance = $property->getValue($service->getAdapter());
        $this->assertInstanceOf('BsbFlysystem\Cache\ZendStorageCache', $cacheInstance);
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
                        ]
                    ]
                ],
            ]
        ];

        $serviceLocatorMock       = $this->getMock('Interop\Container\ContainerInterface');
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter           = new NullAdapter();
        $adapterPluginMock = $this->getMock('Interop\Container\ContainerInterface');
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $service = $factory($serviceLocatorMock, 'named_fs');

        //works becuase plugin is registered
        $this->assertEmpty($service->listPaths());
    }
}

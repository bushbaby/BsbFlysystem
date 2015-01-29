<?php

namespace BsbFlysystemTest\Service\Factory;

use BsbFlysystem\Filesystem\Factory\FilesystemAbstractFactory;
use BsbFlysystem\Service\Factory\FilesystemManagerFactory;
use BsbFlysystemTest\Framework\TestCase;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListPaths;

class FilesystemAbstractFactoryTest extends TestCase
{
    public function testCanCreateService()
    {
        $factory = new FilesystemAbstractFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'known' => []
                ]
            ]
        ];

        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->any())->method('get')->with('config')->willReturn($config);

        $this->assertTrue($factory->canCreateServiceWithName($serviceLocatorMock, 'known', 'known'));
        $this->assertFalse($factory->canCreateServiceWithName($serviceLocatorMock, 'unknown', 'unknown'));
    }

    public function testThrowsExceptionForMissingAdapter()
    {
        $factory = new FilesystemAbstractFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => []
                ]
            ]
        ];

        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->any())->method('get')->with('config')->willReturn($config);

        $this->setExpectedException(
            'UnexpectedValueException',
            "Missing 'adapter' key for the filesystem 'namedfs' configuration"
        );

        $factory->createServiceWithName($serviceLocatorMock, 'namedfs', 'named_fs');
    }

    public function testCreateServiceWithNameReturnsFilesystem()
    {
        $factory = new FilesystemAbstractFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => [
                        'adapter' => 'named_adapter',
                    ]
                ],
            ]
        ];

        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter           = new NullAdapter();
        $adapterPluginMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $service = $factory->createServiceWithName($serviceLocatorMock, 'namedfs', 'named_fs');

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $service);
        $this->assertInstanceOf('League\Flysystem\Filesystem', $service);
        $this->assertNotInstanceOf('League\Flysystem\EventableFilesystem\EventableFilesystem', $service);
    }

    public function testCreateServiceWithNameReturnsEventableFilesystem()
    {
        $factory = new FilesystemAbstractFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => [
                        'adapter' => 'named_adapter',
                        'eventable' => true
                    ]
                ],
            ]
        ];

        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter           = new NullAdapter();
        $adapterPluginMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $service = $factory->createServiceWithName($serviceLocatorMock, 'namedfs', 'named_fs');

        $this->assertInstanceOf('League\Flysystem\FilesystemInterface', $service);
        $this->assertInstanceOf('League\Flysystem\EventableFilesystem\EventableFilesystem', $service);
    }

    public function testCreateServiceWithNameCachedAdapter()
    {
        $factory = new FilesystemAbstractFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => [
                        'adapter' => 'named_adapter',
                        'cache' => 'named/cache'
                    ]
                ],
            ]
        ];

        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter           = new NullAdapter();
        $adapterPluginMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $cacheMock = $this->getMock('League\Flysystem\Cached\CacheInterface');
        $serviceLocatorMock->expects($this->at(2))->method('get')->with('named/cache')->willReturn($cacheMock);

        /** @var Filesystem $service */
        $service = $factory->createServiceWithName($serviceLocatorMock, 'namedfs', 'named_fs');

        $this->assertInstanceOf('League\Flysystem\Cached\CachedAdapter', $service->getAdapter());
    }

    public function testCreateServiceWithNameCachedAdapterZendCacheStorage()
    {
        $factory = new FilesystemAbstractFactory();

        $config = [
            'bsb_flysystem' => [
                'filesystems' => [
                    'named_fs' => [
                        'adapter' => 'named_adapter',
                        'cache' => 'named/cache'
                    ]
                ],
            ]
        ];

        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter           = new NullAdapter();
        $adapterPluginMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $cacheMock = $this->getMock('Zend\Cache\Storage\StorageInterface');
        $serviceLocatorMock->expects($this->at(2))->method('get')->with('named/cache')->willReturn($cacheMock);

        /** @var Filesystem $service */
        $service = $factory->createServiceWithName($serviceLocatorMock, 'namedfs', 'named_fs');

        $this->assertInstanceOf('League\Flysystem\Cached\CachedAdapter', $service->getAdapter());

        $class = new \ReflectionClass('League\Flysystem\Cached\CachedAdapter');
        $property = $class->getProperty("cache");
        $property->setAccessible(true);

        $cacheInstance = $property->getValue($service->getAdapter());
        $this->assertInstanceOf('BsbFlysystem\Cache\ZendStorageCache', $cacheInstance);
    }

    public function testCreateServiceWithNameReturnsFilesystemWithPluginsAdded()
    {
        $factory = new FilesystemAbstractFactory();

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

        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->at(0))->method('get')->with('config')->willReturn($config);

        $adapter           = new NullAdapter();
        $adapterPluginMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->at(1))->method('get')->with('BsbFlysystemAdapterManager')->willReturn($adapterPluginMock);
        $adapterPluginMock->expects($this->once())->method('get')->with('named_adapter')->willReturn($adapter);

        $service = $factory->createServiceWithName($serviceLocatorMock, 'namedfs', 'named_fs');

        //works becuase plugin is registered
        $this->assertEmpty($service->listPaths());
    }
}

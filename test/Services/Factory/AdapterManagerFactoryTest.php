<?php

declare(strict_types=1);

namespace BsbFlysystemTest\Service\Factory;

use BsbFlysystem\Service\AdapterManager;
use BsbFlysystem\Service\Factory\AdapterManagerFactory;
use BsbFlysystemTest\Framework\TestCase;

class AdapterManagerFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $factory = new AdapterManagerFactory();

        $config = [
            'bsb_flysystem' => [
                'adapters' => [],
            ],
        ];

        $serviceLocatorMock = $this->getMockBuilder('Interop\Container\ContainerInterface')->getMock();
        $serviceLocatorMock->expects($this->once())->method('get')->with('config')->willReturn($config);

        $this->assertInstanceOf('BsbFlysystem\Service\AdapterManager', $factory($serviceLocatorMock, null));
    }

    public function testServicesSharedByDefault()
    {
        $factory = new AdapterManagerFactory();
        $config = [
            'bsb_flysystem' => [
                'adapters' => [
                    'named_adapter' => [
                        'type' => 'someadapter',
                        'shared' => true,
                    ],
                ],
                'adapter_map' => [
                    'factories' => [
                        'someadapter' => 'Zend\ServiceManager\Factory\InvokableFactory',
                    ],
                    'aliases' => [
                        'someadapter' => 'Some/Adapter',
                    ],
                ],
            ],
        ];

        $serviceLocatorMock = $this->getMockBuilder('Interop\Container\ContainerInterface')->getMock();
        $serviceLocatorMock->expects($this->once())->method('get')->with('config')->willReturn($config);

        /** @var AdapterManager $adapterManager */
        $adapterManager = $factory($serviceLocatorMock, null);
    }

    public function testThrowsExceptionForMissingAdapterType()
    {
        $factory = new AdapterManagerFactory();

        $config = [
            'bsb_flysystem' => [
                'adapters' => [
                    'named_adapter' => [],
                ],
            ],
        ];

        $serviceLocatorMock = $this->getMockBuilder('Interop\Container\ContainerInterface')->getMock();
        $serviceLocatorMock->expects($this->once())->method('get')->with('config')->willReturn($config);

        $this->expectException(
            'UnexpectedValueException',
            "Missing 'type' key for the adapter 'named_adapter' configuration"
        );
        $factory($serviceLocatorMock, null);
    }

    public function testThrowsExceptionForUnknownAdapterType()
    {
        $factory = new AdapterManagerFactory();

        $config = [
            'bsb_flysystem' => [
                'adapters' => [
                    'named_adapter' => [
                        'type' => 'unknown_adapter',
                    ],
                ],
            ],
        ];

        $serviceLocatorMock = $this->getMockBuilder('Interop\Container\ContainerInterface')->getMock();
        $serviceLocatorMock->expects($this->once())->method('get')->with('config')->willReturn($config);

        $this->expectException('BsbFlysystem\Exception\UnexpectedValueException');
        $factory($serviceLocatorMock, null);
    }
}

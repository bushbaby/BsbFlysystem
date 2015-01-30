<?php

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
                'adapters' => []
            ]
        ];

        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->once())->method('get')->with('config')->willReturn($config);

        $this->assertInstanceOf('BsbFlysystem\Service\AdapterManager', $factory->createService($serviceLocatorMock));
    }

    public function testServicesSharedByDefault()
    {
        $adapterMap = [
            'invokables' => [
                'someadapter' => 'Some/Adapter'
            ]
        ];
        $factory    = new AdapterManagerFactory($adapterMap);
        $config     = [
            'bsb_flysystem' => [
                'adapters' => [
                    'named_adapter' => [
                        'type'   => 'someadapter',
                        'shared' => true,
                    ]
                ]
            ]
        ];

        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->once())->method('get')->with('config')->willReturn($config);

        /** @var AdapterManager $adapterManager */
        $adapterManager = $factory->createService($serviceLocatorMock);

        $this->assertTrue($adapterManager->isShared('named_adapter'));
    }

    public function testThrowsExceptionForMissingAdapterType()
    {
        $factory = new AdapterManagerFactory();

        $config = [
            'bsb_flysystem' => [
                'adapters' => [
                    'named_adapter' => [],
                ]
            ]
        ];

        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->once())->method('get')->with('config')->willReturn($config);

        $this->setExpectedException(
            'UnexpectedValueException',
            "Missing 'type' key for the adapter 'named_adapter' configuration"
        );
        $factory->createService($serviceLocatorMock);
    }

    public function testThrowsExceptionForUnknownAdapterType()
    {
        $factory = new AdapterManagerFactory();

        $config = [
            'bsb_flysystem' => [
                'adapters' => [
                    'named_adapter' => [
                        'type' => 'unknown_adapter'
                    ],
                ]
            ]
        ];

        $serviceLocatorMock = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocatorMock->expects($this->once())->method('get')->with('config')->willReturn($config);

        $this->setExpectedException('UnexpectedValueException', "Unknown adapter type 'unknown_adapter'");
        $factory->createService($serviceLocatorMock);
    }
}

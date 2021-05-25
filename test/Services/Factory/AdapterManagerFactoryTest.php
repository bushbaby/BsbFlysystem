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

namespace BsbFlysystemTest\Service\Factory;

use BsbFlysystem\Service\AdapterManager;
use BsbFlysystem\Service\Factory\AdapterManagerFactory;
use PHPUnit\Framework\TestCase;

class AdapterManagerFactoryTest extends TestCase
{
    public function testCreateService(): void
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

    public function testServicesSharedByDefault(): void
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
                        'someadapter' => 'Laminas\ServiceManager\Factory\InvokableFactory',
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

    public function testThrowsExceptionForMissingAdapterType(): void
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

    public function testThrowsExceptionForUnknownAdapterType(): void
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

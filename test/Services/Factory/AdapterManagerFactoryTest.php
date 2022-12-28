<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014 bushbaby multimedia. (https://bushbaby.nl)
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
use Prophecy\Prophet;
use Psr\Container\ContainerInterface;

class AdapterManagerFactoryTest extends TestCase
{
    protected Prophet $prophet;

    protected function setUp(): void
    {
        $this->prophet = new Prophet();
    }

    protected function tearDown(): void
    {
        $this->prophet->checkPredictions();
    }

    public function testCreateService(): void
    {
        $container = $this->prophet->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'bsb_flysystem' => [
                'adapters' => [
                ],
            ],
        ]);

        $adapterManager = (new AdapterManagerFactory())($container->reveal(), null);

        $this->assertInstanceOf(AdapterManager::class, $adapterManager);
    }

    public function testThrowsExceptionForMissingAdapterFactory(): void
    {
        $container = $this->prophet->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'bsb_flysystem' => [
                'adapters' => [
                    'named_adapter' => [],
                ],
            ],
        ]);

        $this->expectException(\AssertionError::class);
        $this->expectExceptionMessage("Option 'factory' must be defined in an adapter configuration");

        (new AdapterManagerFactory())($container->reveal(), null);
    }
}

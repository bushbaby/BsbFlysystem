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

namespace BsbFlysystemTest\Filesystem\Factory;

use BsbFlysystem\Filesystem\Factory\FilesystemFactory;
use BsbFlysystem\Service\AdapterManager;
use Interop\Container\ContainerInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class FilesystemFactoryTest extends TestCase
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

    public function testConfigMissingAdapterName(): void
    {
        $container = $this->prophet->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
                'bsb_flysystem' => [
                    'filesystems' => ['not-an-array'],
                ],
            ]);

        $this->expectException(\AssertionError::class);

        (new FilesystemFactory())($container->reveal(), 'named_fs');
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

        $container = $this->prophet->prophesize(ContainerInterface::class);
        $adapterManager = $this->prophet->prophesize(AdapterManager::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);

        $adapter = new InMemoryFilesystemAdapter();

        $adapterManager->get('named_adapter', null)->willReturn($adapter);
        $container->get(AdapterManager::class)->willReturn($adapterManager->reveal());

        $service = $factory($container->reveal(), 'named_fs');

        $this->assertInstanceOf(Filesystem::class, $service);
    }
}

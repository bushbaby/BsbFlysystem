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

namespace BsbFlysystemTest\Adapter\Factory;

use Ajgl\Flysystem\Replicate\ReplicateFilesystemAdapter;
use BsbFlysystem\Adapter\Factory\ReplicateAdapterFactory;
use BsbFlysystem\Service\AdapterManager;
use League\Flysystem\FilesystemAdapter;
use Psr\Container\ContainerInterface;

class ReplicateAdapterFactoryTest extends BaseAdapterFactory
{
    public function testGettingFromServiceManager(): void
    {
        $factory = new ReplicateAdapterFactory();

        $container = $this->prophet->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);

        $manager = $this->prophet->prophesize(AdapterManager::class);
        $container->get(AdapterManager::class)->willReturn($manager->reveal());

        $source = $this->prophet->prophesize(FilesystemAdapter::class);
        $manager->get('a-source')->willReturn($source->reveal());

        $replica = $this->prophet->prophesize(FilesystemAdapter::class);
        $manager->get('a-replica')->willReturn($replica->reveal());

        $adapter = $factory($container->reveal(), 'replicate_default', [
            'source' => 'a-source',
            'replica' => 'a-replica',
        ]);

        $this->assertInstanceOf(ReplicateFilesystemAdapter::class, $adapter);
    }
}

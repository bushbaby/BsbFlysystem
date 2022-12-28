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

use BsbFlysystem\Adapter\Factory\InMemoryAdapterFactory;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\MimeTypeDetection\MimeTypeDetector;
use Psr\Container\ContainerInterface;

class InMemoryAdapterFactoryTest extends BaseAdapterFactory
{
    public function testGettingFromServiceManager(): void
    {
        $factory = new InMemoryAdapterFactory();

        $container = $this->prophet->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);

        $mimeTypeDetector = $this->prophet->prophesize(MimeTypeDetector::class);
        $container->get('a-mime-type-detector')->willReturn($mimeTypeDetector->reveal());

        $adapter = $factory($container->reveal(), 'inmemory_default', [
            'mimeTypeDetector' => 'a-mime-type-detector',
        ]);

        $this->assertInstanceOf(InMemoryFilesystemAdapter::class, $adapter);
    }
}

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

use BsbFlysystem\Adapter\Factory\LocalAdapterFactory;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use League\MimeTypeDetection\MimeTypeDetector;
use Psr\Container\ContainerInterface;

class LocalAdapterFactoryTest extends BaseAdapterFactory
{
    public function testGettingFromServiceManager(): void
    {
        $factory = new LocalAdapterFactory();

        $container = $this->prophet->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);

        $mimeTypeDetector = $this->prophet->prophesize(MimeTypeDetector::class);
        $container->get('a-mime-type-detector')->willReturn($mimeTypeDetector->reveal());

        $visibility = $this->prophet->prophesize(VisibilityConverter::class);
        $visibility->defaultForDirectories()->willReturn(0755);
        $container->get('a-visibility')->willReturn($visibility->reveal());

        $adapter = $factory($container->reveal(), 'local_default', [
            'location' => 'a-location',
            'mimeTypeDetector' => 'a-mime-type-detector',
            'visibility' => 'a-visibility',
        ]);

        $this->assertInstanceOf(LocalFilesystemAdapter::class, $adapter);
    }
}

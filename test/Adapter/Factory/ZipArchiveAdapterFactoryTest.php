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

use BsbFlysystem\Adapter\Factory\ZipArchiveAdapterFactory;
use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use League\MimeTypeDetection\MimeTypeDetector;
use phpmock\phpunit\PHPMock;
use Psr\Container\ContainerInterface;

class ZipArchiveAdapterFactoryTest extends BaseAdapterFactory
{
    use PHPMock;

    public function testClassExists(): void
    {
        $classExists = $this->getFunctionMock('BsbFlysystem\Adapter\Factory', 'class_exists');
        $classExists->expects($this->once())->willReturn(false);

        $factory = new ZipArchiveAdapterFactory();
        $container = $this->prophet->prophesize(ContainerInterface::class);

        $this->expectException(RequirementsException::class);
        $factory->doCreateService($container->reveal());
    }

    public function testGettingFromServiceManager(): void
    {
        $factory = new ZipArchiveAdapterFactory();

        $container = $this->prophet->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);

        $mimeTypeDetector = $this->prophet->prophesize(MimeTypeDetector::class);
        $container->get('a-mime-type-detector')->willReturn($mimeTypeDetector->reveal());

        $visibility = $this->prophet->prophesize(VisibilityConverter::class);
        $container->get('a-visibility')->willReturn($visibility->reveal());

        $adapter = $factory($container->reveal(), 'zip_default', [
            'zipArchiveProvider' => [
                'filename' => 'test.zip',
            ],
            'root' => 'a-root',
            'mimeTypeDetector' => 'a-mime-type-detector',
            'visibility' => 'a-visibility',
        ]);

        $this->assertInstanceOf(ZipArchiveAdapter::class, $adapter);
    }
}

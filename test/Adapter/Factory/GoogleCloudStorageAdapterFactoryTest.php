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

use BsbFlysystem\Adapter\Factory\GoogleCloudStorageAdapterFactory;
use BsbFlysystem\Exception\RequirementsException;
use Google\Cloud\Storage\Bucket;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use League\Flysystem\GoogleCloudStorage\VisibilityHandler;
use League\MimeTypeDetection\MimeTypeDetector;
use phpmock\phpunit\PHPMock;
use Psr\Container\ContainerInterface;

class GoogleCloudStorageAdapterFactoryTest extends BaseAdapterFactory
{
    use PHPMock;

    public function testClassExists(): void
    {
        $classExists = $this->getFunctionMock('BsbFlysystem\Adapter\Factory', 'class_exists');
        $classExists->expects($this->once())->willReturn(false);

        $factory = new GoogleCloudStorageAdapterFactory();
        $container = $this->prophet->prophesize(ContainerInterface::class);

        $this->expectException(RequirementsException::class);
        $factory->doCreateService($container->reveal());
    }

    public function testGettingFromServiceManager(): void
    {
        $factory = new GoogleCloudStorageAdapterFactory();

        $container = $this->prophet->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);

        $bucket = $this->prophet->prophesize(Bucket::class);
        $container->get('a-bucket')->willReturn($bucket->reveal());

        $mimeTypeDetector = $this->prophet->prophesize(MimeTypeDetector::class);
        $container->get('a-mime-type-detector')->willReturn($mimeTypeDetector->reveal());

        $visibility = $this->prophet->prophesize(VisibilityHandler::class);
        $container->get('a-visibility')->willReturn($visibility->reveal());

        $adapter = $factory($container->reveal(), 'googlecloudstorage_default', [
            'bucket' => 'a-bucket',
            'mimeTypeDetector' => 'a-mime-type-detector',
            'visibilityHandler' => 'a-visibility',
        ]);

        $this->assertInstanceOf(GoogleCloudStorageAdapter::class, $adapter);
    }
}

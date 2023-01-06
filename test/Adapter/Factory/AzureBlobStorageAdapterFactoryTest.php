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

use BsbFlysystem\Adapter\Factory\AzureBlobStorageAdapterFactory;
use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use League\MimeTypeDetection\MimeTypeDetector;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use phpmock\phpunit\PHPMock;
use Psr\Container\ContainerInterface;

class AzureBlobStorageAdapterFactoryTest extends BaseAdapterFactory
{
    use PHPMock;

    public function testClassExists(): void
    {
        $classExists = $this->getFunctionMock('BsbFlysystem\Adapter\Factory', 'class_exists');
        $classExists->expects($this->once())->willReturn(false);

        $factory = new AzureBlobStorageAdapterFactory();
        $container = $this->prophet->prophesize(ContainerInterface::class);

        $this->expectException(RequirementsException::class);
        $factory->doCreateService($container->reveal());
    }

    public function testGettingFromServiceManager(): void
    {
        $factory = new AzureBlobStorageAdapterFactory();

        $container = $this->prophet->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);

        $mimeTypeDetector = $this->prophet->prophesize(MimeTypeDetector::class);
        $container->get('a-mime-type-detector')->willReturn($mimeTypeDetector->reveal());

        $client = $this->prophet->prophesize(BlobRestProxy::class);
        $container->get('a-client')->willReturn($client->reveal());

        $adapter = $factory($container->reveal(), 'azureblobstorage_default', [
            'client' => 'a-client',
            'container' => 'xxx',
            'mimeTypeDetector' => 'a-mime-type-detector',
        ]);

        $this->assertInstanceOf(AzureBlobStorageAdapter::class, $adapter);
    }

    public function testClientOptions(): void
    {
        $factory = new AzureBlobStorageAdapterFactory();

        $container = $this->prophet->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);

        $adapter = $factory($container->reveal(), 'azureblobstorage_default', [
            'container' => 'xxx',
            'client' => ['connectionString' => 'DefaultEndpointsProtocol=https;AccountName=xxx;AccountKey=xxx'],
        ]);

        $this->assertInstanceOf(AzureBlobStorageAdapter::class, $adapter);
    }

    public function testServiceSettingsOptions(): void
    {
        $factory = new AzureBlobStorageAdapterFactory();

        $container = $this->prophet->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);

        $adapter = $factory($container->reveal(), 'azureblobstorage_default', [
            'container' => 'xxx',
            'client' => ['connectionString' => 'DefaultEndpointsProtocol=https;AccountName=xxx;AccountKey=xxx'],
            'serviceSettings' => [
                'name' => 'xxx',
                 'key' => 'xxx',
                 'blobEndpointUri' => 'https://xxx.blob.core.windows.net/',
                  'queueEndpointUri' => 'https://xxx.queue.core.windows.net/',
                  'tableEndpointUri' => 'https://xxx.table.core.windows.net/',
                'fileEndpointUri' => 'https://xxx.file.core.windows.net/', ],
        ]);

        $this->assertInstanceOf(AzureBlobStorageAdapter::class, $adapter);
    }
}

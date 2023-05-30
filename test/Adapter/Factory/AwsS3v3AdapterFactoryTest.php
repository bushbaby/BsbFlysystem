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

use Aws\Command;
use BsbFlysystem\Adapter\Factory\AwsS3v3AdapterFactory;
use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystemTest\Bootstrap;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\AwsS3V3\VisibilityConverter;
use League\MimeTypeDetection\MimeTypeDetector;
use phpmock\phpunit\PHPMock;
use Psr\Container\ContainerInterface;

class AwsS3v3AdapterFactoryTest extends BaseAdapterFactory
{
    use PHPMock;

    public function testClassExists(): void
    {
        $classExists = $this->getFunctionMock('BsbFlysystem\Adapter\Factory', 'class_exists');
        $classExists->expects($this->once())->willReturn(false);

        $factory = new AwsS3v3AdapterFactory();
        $container = $this->prophet->prophesize(ContainerInterface::class);

        $this->expectException(RequirementsException::class);
        $factory->doCreateService($container->reveal());
    }

    public function testCreateService(): void
    {
        $sm = Bootstrap::getServiceManager();
        $factory = new AwsS3v3AdapterFactory();

        $adapter = $factory($sm, 'awss3v3_default');

        $this->assertInstanceOf(AwsS3V3Adapter::class, $adapter);
    }

    public function testGettingFromServiceManager(): void
    {
        $factory = new AwsS3v3AdapterFactory();

        $container = $this->prophet->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);

        $visibility = $this->prophet->prophesize(VisibilityConverter::class);
        $container->get('a-visibility')->willReturn($visibility->reveal());

        $mimeTypeDetector = $this->prophet->prophesize(MimeTypeDetector::class);
        $container->get('a-mime-type-detector')->willReturn($mimeTypeDetector->reveal());

        $adapter = $factory($container->reveal(), 'awss3v3_default', [
            'client' => [
                'credentials' => [
                    'key' => 'abc',
                    'secret' => 'xxx',
                ],
                'region' => 'eu-west-1',
                'version' => 'latest',
            ],
            'bucket' => 'xxxxx',
            'visibility' => 'a-visibility',
            'mimeTypeDetector' => 'a-mime-type-detector',
            'iam' => false,
        ]);

        $this->assertInstanceOf(AwsS3V3Adapter::class, $adapter);
    }

    public function testCreateServiceWithRequestOptions(): void
    {
        $options = [
            'client' => [
                'credentials' => [
                    'key' => 'abc',
                    'secret' => 'def',
                ],
                'region' => 'ghi',
                'http' => [
                    'timeout' => 1,
                ],
            ],
            'bucket' => 'jkl',
        ];

        $sm = Bootstrap::getServiceManager();
        $factory = new AwsS3v3AdapterFactory($options);

        /** @var AwsS3V3Adapter $adapter */
        $adapter = $factory($sm, 'awss3v3_default');

        $reflectionProperty = new \ReflectionProperty($adapter, 'client');
        $reflectionProperty->setAccessible(true);
        $client = $reflectionProperty->getValue($adapter);
        $reflectionProperty->setAccessible(false);

        /** @var Command $command */
        $command = $client->getCommand('GetObject');

        self::assertTrue($command->hasParam('@http'));
        self::assertEquals(['timeout' => 1], $command['@http']);
    }
}

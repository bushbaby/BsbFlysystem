<?php

declare(strict_types=1);

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\AwsS3v3AdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AwsS3v3AdapterFactoryTest.
 *
 * @version     1.0
 *
 * @author      Julien Guittard <julien.guittard@mme.com>
 */
class AwsS3v3AdapterFactoryTest extends TestCase
{
    /**
     * @var \ReflectionProperty
     */
    protected $property;

    /**
     * @var \ReflectionMethod
     */
    protected $method;

    public function setup()
    {
        $class = new \ReflectionClass('BsbFlysystem\Adapter\Factory\AwsS3v3AdapterFactory');
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService()
    {
        $this->markTestSkipped('Skipped because Aws3Sv2 and Aws3Sv3 are not compatible.');

        $sm = Bootstrap::getServiceManager();
        $factory = new AwsS3v3AdapterFactory();

        $adapter = $factory($sm, 'awss3_default');

        $this->assertInstanceOf('League\Flysystem\AwsS3v3\AwsS3v3Adapter', $adapter);
    }

    /**
     * @dataProvider validateConfigProvider
     */
    public function testValidateConfig(
        $options,
        $expectedOptions = false,
        $expectedException = false,
        $expectedExceptionMessage = false
    ) {
        $factory = new AwsS3v3AdapterFactory($options);

        if ($expectedException) {
            $this->expectException($expectedException);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $this->method->invokeArgs($factory, []);

        if (is_array($expectedOptions)) {
            $this->assertEquals($expectedOptions, $this->property->getValue($factory));
        }
    }

    public function validateConfigProvider(): array
    {
        return [
            [
                [],
                [],
                'UnexpectedValueException',
                "Missing 'credentials' as array",
            ],
            [
                [
                    'iam' => false,
                ],
                [],
                'UnexpectedValueException',
                "Missing 'credentials' as array",
            ],
            [
                [
                    'iam' => true,
                ],
                [],
                'UnexpectedValueException',
                "Missing 'region' as option",
            ],
            [
                [
                    'credentials' => [],
                ],
                [],
                'UnexpectedValueException',
                "Missing 'key' as option",
            ],
            [
                [
                    'credentials' => [
                        'key' => 'foo',
                    ],
                ],
                [],
                'UnexpectedValueException',
                "Missing 'secret' as option",
            ],
            [
                [
                    'credentials' => [
                        'key' => 'foo',
                        'secret' => 'bar',
                    ],
                ],
                [],
                'UnexpectedValueException',
                "Missing 'region' as option",
            ],
            [
                [
                    'iam' => true,
                    'credentials' => [
                        'key' => 'foo',
                        'secret' => 'bar',
                    ],
                ],
                [],
                'UnexpectedValueException',
                "Missing 'region' as option",
            ],
            [
                [
                    'credentials' => [
                        'key' => 'foo',
                        'secret' => 'bar',
                    ],
                    'region' => 'baz',
                ],
                [],
                'UnexpectedValueException',
                "Missing 'bucket' as option",
            ],
            [
                [
                    'credentials' => [
                        'key' => 'abc',
                        'secret' => 'def',
                    ],
                    'region' => 'ghi',
                    'bucket' => 'jkl',
                ],
                [
                    'credentials' => [
                        'key' => 'abc',
                        'secret' => 'def',
                    ],
                    'region' => 'ghi',
                    'bucket' => 'jkl',
                    'prefix' => '',
                    'request.options' => [],
                    'version' => 'latest',
                ],
            ],
        ];
    }

    public function testDoCreateService() {
        $options = [
            'credentials'     => [
                'key'    => 'abc',
                'secret' => 'def',
            ],
            'region'          => 'ghi',
            'bucket'          => 'jkl',
        ];
        $factory = new AwsS3v3AdapterFactory($options);

        $this->method->invokeArgs($factory, []);

        /** @var ServiceLocatorInterface $container */
        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $adapter = $factory->doCreateService($container);
        self::assertInstanceOf(\League\Flysystem\AwsS3v3\AwsS3Adapter::class, $adapter);

        /** @var \League\Flysystem\AwsS3v3\AwsS3Adapter $adapter */
        $command = $adapter->getClient()->getCommand('GetObject');
        self::assertInstanceOf(\Aws\Command::class, $command);
    }

    public function testDoCreateServiceWithRequestOptions() {
        $options = [
            'credentials'     => [
                'key'    => 'abc',
                'secret' => 'def',
            ],
            'region'          => 'ghi',
            'bucket'          => 'jkl',
            'request.options' => [
                'timeout' => 1,
            ],
        ];
        $factory = new AwsS3v3AdapterFactory($options);

        $this->method->invokeArgs($factory, []);

        /** @var ServiceLocatorInterface $container */
        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        /** @var \League\Flysystem\AwsS3v3\AwsS3Adapter $adapter */
        $adapter = $factory->doCreateService($container);

        /** @var \Aws\Command $command */
        $command = $adapter->getClient()->getCommand('GetObject');

        self::assertTrue($command->hasParam('@http'));
        self::assertEquals(['timeout' => 1], $command['@http']);
    }
}

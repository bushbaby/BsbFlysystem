<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2021 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystemTest\Adapter\Factory;

use Aws\Command;
use BsbFlysystem\Adapter\Factory\AwsS3v3AdapterFactory;
use BsbFlysystemTest\Bootstrap;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class AwsS3v3AdapterFactoryTest extends TestCase
{
    /**
     * @var ReflectionProperty
     */
    protected $property;

    /**
     * @var ReflectionMethod
     */
    protected $method;

    public function setup(): void
    {
        $class = new ReflectionClass('BsbFlysystem\Adapter\Factory\AwsS3v3AdapterFactory');
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService(): void
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
        array $options,
        ?array $expectedOptions,
        ?string $expectedException,
        ?string $expectedExceptionMessage
    ): void {
        $factory = new AwsS3v3AdapterFactory($options);

        if ($expectedException) {
            $this->expectException($expectedException);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $this->method->invokeArgs($factory, []);

        if (\is_array($expectedOptions)) {
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
                    'streamReads' => true,
                ],
                null,
                null,
            ],
        ];
    }

    public function testCreateServiceWithRequestOptions(): void
    {
        $this->markTestSkipped('Skipped because Aws3Sv2 and Aws3Sv3 are not compatible.');

        $options = [
            'credentials' => [
                'key' => 'abc',
                'secret' => 'def',
            ],
            'region' => 'ghi',
            'bucket' => 'jkl',
            'request.options' => [
                'timeout' => 1,
            ],
        ];

        $sm = Bootstrap::getServiceManager();
        $factory = new AwsS3v3AdapterFactory($options);

        /** @var AwsS3Adapter $adapter */
        $adapter = $factory($sm, 'awss3_default');

        /** @var Command $command */
        $command = $adapter->getClient()->getCommand('GetObject');

        self::assertTrue($command->hasParam('@http'));
        self::assertEquals(['timeout' => 1], $command['@http']);
    }
}

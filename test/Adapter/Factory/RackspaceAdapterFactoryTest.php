<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2019 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\RackspaceAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use League\Flysystem\Rackspace\RackspaceAdapter;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class RackspaceAdapterFactoryTest extends TestCase
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
        $class = new ReflectionClass(RackspaceAdapterFactory::class);
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService(): void
    {
        $sm = Bootstrap::getServiceManager();
        $factory = new RackspaceAdapterFactory();

        $adapter = $factory($sm, 'rackspace_default');

        $this->assertInstanceOf(RackspaceAdapter::class, $adapter);
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
        $factory = new RackspaceAdapterFactory($options);

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
                "Missing 'url' as option",
            ],
            [
                ['url' => 'some_url'],
                [],
                'UnexpectedValueException',
                "Missing 'secret' as option",
            ],
            [
                [
                    'url' => 'some_url',
                    'secret' => 'secret',
                ],
                [],
                'UnexpectedValueException',
                "Missing 'secret' as option",
            ],
            [
                [
                    'url' => 'some_url',
                    'secret' => [],
                ],
                [],
                'UnexpectedValueException',
                "Missing 'objectstore' as option",
            ],
            [
                [
                    'url' => 'some_url',
                    'secret' => [
                        'username' => 'foo',
                        'password' => 'foo',
                        'tenant_name' => 'foo',
                    ],
                    'objectstore' => 'bar',
                ],
                [],
                'UnexpectedValueException',
                "Missing 'objectstore' as option",
            ],
            [
                [
                    'url' => 'some_url',
                    'secret' => [
                        'username' => 'foo',
                        'password' => 'foo',
                        'tenant_name' => 'foo',
                    ],
                    'objectstore' => [],
                ],
                [],
                'UnexpectedValueException',
                "Missing 'objectstore.name' as option",
            ],
            [
                [
                    'url' => 'some_url',
                    'secret' => [
                        'username' => 'foo',
                        'password' => 'foo',
                        'tenant_name' => 'foo',
                    ],
                    'objectstore' => [
                        'name' => 'foo',
                    ],
                ],
                [],
                'UnexpectedValueException',
                "Missing 'objectstore.region' as option",
            ],
            [
                [
                    'url' => 'some_url',
                    'secret' => [],
                    'objectstore' => [
                        'name' => 'foo',
                        'region' => 'foo',
                    ],
                ],
                [],
                'UnexpectedValueException',
                "Missing 'objectstore.container' as option",
            ],
            [
                [
                    'url' => 'some_url',
                    'secret' => [],
                    'objectstore' => [
                        'name' => 'foo',
                        'region' => 'foo',
                        'container' => 'foo',
                    ],
                ],
                [
                    'url' => 'some_url',
                    'secret' => [],
                    'objectstore' => [
                        'name' => 'foo',
                        'region' => 'foo',
                        'container' => 'foo',
                        'url_type' => null, // added
                    ],
                    'options' => [], // added
                    'prefix' => null, // added
                ],
                null,
                null,
            ],
        ];
    }
}

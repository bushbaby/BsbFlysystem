<?php

declare(strict_types=1);

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\RackspaceAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;
use League\Flysystem\Rackspace\RackspaceAdapter;

class RackspaceAdapterFactoryTest extends TestCase
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
        $class = new \ReflectionClass(RackspaceAdapterFactory::class);
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService()
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
        $options,
        $expectedOptions = false,
        $expectedException = false,
        $expectedExceptionMessage = false
    ) {
        $factory = new RackspaceAdapterFactory($options);

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
            ],
        ];
    }
}

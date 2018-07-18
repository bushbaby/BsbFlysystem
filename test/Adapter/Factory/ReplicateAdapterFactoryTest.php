<?php

declare(strict_types=1);

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\ReplicateAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;
use League\Flysystem\Replicate\ReplicateAdapter;

class ReplicateAdapterFactoryTest extends TestCase
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
        $class = new \ReflectionClass(ReplicateAdapterFactory::class);
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService()
    {
        $sm = Bootstrap::getServiceManager();
        $factory = new ReplicateAdapterFactory();

        $adapter = $factory($sm, 'replicate_default');

        $this->assertInstanceOf(ReplicateAdapter::class, $adapter);
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
        $factory = new ReplicateAdapterFactory($options);

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
                false,
                'UnexpectedValueException',
                "Missing 'source' as option",
            ],
            [
                ['source' => 'local->default'],
                false,
                'UnexpectedValueException',
                "Missing 'replicate' as option",
            ],
            [
                ['source' => 'local->default', 'replicate' => 'zip->default'],
                ['source' => 'local->default', 'replicate' => 'zip->default'],
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\ReplicateAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;

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
        $class          = new \ReflectionClass('BsbFlysystem\Adapter\Factory\ReplicateAdapterFactory');
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService()
    {
        $sm      = Bootstrap::getServiceManager();
        $factory = new ReplicateAdapterFactory();

        $adapter = $factory($sm, 'replicate_default');

        $this->assertInstanceOf('League\Flysystem\Replicate\ReplicateAdapter', $adapter);
    }

    /**
     * @dataProvider validateConfigProvider
     *
     * @param      $options
     * @param bool $expectedOptions
     * @param bool $expectedException
     * @param bool $expectedExceptionMessage
     */
    public function testValidateConfig(
        $options,
        $expectedOptions = false,
        $expectedException = false,
        $expectedExceptionMessage = false
    ) {
        $factory = new ReplicateAdapterFactory($options);

        if ($expectedException) {
            $this->expectException($expectedException, $expectedExceptionMessage);
        }

        $this->method->invokeArgs($factory, []);

        if (is_array($expectedOptions)) {
            $this->assertEquals($expectedOptions, $this->property->getValue($factory));
        }
    }

    /**
     * @return array
     */
    public function validateConfigProvider()
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

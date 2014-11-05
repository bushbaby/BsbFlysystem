<?php

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\ZipAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;

class ZipAdapterFactoryTest extends TestCase
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
        $class          = new \ReflectionClass('BsbFlysystem\Adapter\Factory\ZipAdapterFactory');
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService()
    {
        $sm      = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $factory = new ZipAdapterFactory();

        $adapter = $factory->createService($manager, 'zipdefault', 'zip_default');

        $this->assertInstanceOf('League\Flysystem\Adapter\Zip', $adapter);
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
        $factory = new ZipAdapterFactory($options);

        if ($expectedException) {
            $this->setExpectedException($expectedException, $expectedExceptionMessage);
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
                "Missing 'archive' as option"
            ],
            [
                ['archive' => 'foo'],
                ['archive' => 'foo', 'prefix' => null]
            ],
        ];
    }
}

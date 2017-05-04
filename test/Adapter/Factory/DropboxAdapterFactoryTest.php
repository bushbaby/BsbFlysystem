<?php

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\DropboxAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;

class DropboxAdapterFactoryTest extends TestCase
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
        $class          = new \ReflectionClass('BsbFlysystem\Adapter\Factory\DropboxAdapterFactory');
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

//    public function testCreateService()
//    {
//        $sm      = Bootstrap::getServiceManager();
//        $factory = new DropboxAdapterFactory();
//
//        $adapter = $factory($sm, 'dropbox_default');
//
//        $this->assertInstanceOf('Spatie\FlysystemDropbox\DropboxAdapter', $adapter);
//    }

    /**
     * @dataProvider validateConfigProvider
     */
    public function testValidateConfig(
        $options,
        $expectedOptions = false,
        $expectedException = false,
        $expectedExceptionMessage = false
    ) {
        $factory = new DropboxAdapterFactory($options);

        if ($expectedException) {
            $this->setExpectedException($expectedException, $expectedExceptionMessage);
        }

        $this->method->invokeArgs($factory, []);

        if (is_array($expectedOptions)) {
            $this->assertEquals($expectedOptions, $this->property->getValue($factory));
        }
    }

    public function validateConfigProvider()
    {
        return [
            [
                [],
                [],
                'UnexpectedValueException',
                "Missing 'access_token' as option"
            ],
            [
                ['access_token' => 'foo'],
                ['access_token' => 'foo', 'prefix' => null]
            ],
        ];
    }
}

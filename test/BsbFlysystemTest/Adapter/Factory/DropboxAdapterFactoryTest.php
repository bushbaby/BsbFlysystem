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

    public function testCreateService()
    {
        $sm      = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $factory = new DropboxAdapterFactory();

        $adapter = $factory->createService($manager, 'dropboxdefault', 'dropbox_default');

        $this->assertInstanceOf('League\Flysystem\Adapter\Dropbox', $adapter);
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
                [],
                'UnexpectedValueException',
                "Missing 'client_identifier' as option"
            ],
            [
                ['access_token' => 'foo', 'client_identifier' => 'foo'],
                ['access_token' => 'foo', 'client_identifier' => 'foo', 'user_locale' => null, 'prefix' => null]
            ],
        ];
    }
}

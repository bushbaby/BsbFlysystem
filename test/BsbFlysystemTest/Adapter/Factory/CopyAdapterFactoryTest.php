<?php

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\CopyAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;

class CopyAdapterFactoryTest extends TestCase
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
        $class          = new \ReflectionClass('BsbFlysystem\Adapter\Factory\CopyAdapterFactory');
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService()
    {
        $sm      = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $factory = new CopyAdapterFactory();

        $adapter = $factory->createService($manager, 'copydefault', 'copy_default');

        $this->assertInstanceOf('League\Flysystem\Adapter\Copy', $adapter);
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
        $factory = new CopyAdapterFactory($options);

        if ($expectedException) {
            $this->setExpectedException($expectedException, $expectedExceptionMessage);
        }

        $this->method->invokeArgs($factory, array());

        if (is_array($expectedOptions)) {
            $this->assertEquals($expectedOptions, $this->property->getValue($factory));
        }
    }

    public function validateConfigProvider()
    {
        return array(
            array(
                array(),
                array(),
                'UnexpectedValueException',
                "Missing 'consumer_key' as option"
            ),
            array(
                array('consumer_key' => 'xxx'),
                array(),
                'UnexpectedValueException',
                "Missing 'consumer_secret' as option"
            ),
            array(
                array('consumer_key' => 'xxx', 'consumer_secret' => 'xxx'),
                array(),
                'UnexpectedValueException',
                "Missing 'access_token' as option"
            ),
            array(
                array('consumer_key' => 'xxx', 'consumer_secret' => 'xxx', 'access_token' => 'xxx'),
                array(),
                'UnexpectedValueException',
                "Missing 'token_secret' as option"
            ),
            array(
                array(
                    'consumer_key'    => 'xxx',
                    'consumer_secret' => 'xxx',
                    'access_token'    => 'xxx',
                    'token_secret'    => 'xxx'
                ),
                array(
                    'consumer_key'    => 'xxx',
                    'consumer_secret' => 'xxx',
                    'access_token'    => 'xxx',
                    'token_secret'    => 'xxx',
                    'prefix'          => null
                ),
            ),
        );
    }
}

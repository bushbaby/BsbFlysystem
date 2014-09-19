<?php

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\FtpAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;

class FtpAdapterFactoryTest extends TestCase
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
        $class          = new \ReflectionClass('BsbFlysystem\Adapter\Factory\FtpAdapterFactory');
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService()
    {
        $sm      = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $factory = new FtpAdapterFactory();

        $adapter = $factory->createService($manager, 'ftpdefault', 'ftp_default');

        $this->assertInstanceOf('League\Flysystem\Adapter\Ftp', $adapter);
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
        $factory = new FtpAdapterFactory($options);

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
                false,
                'UnexpectedValueException',
                "Missing 'host' as option"
            ),
            array(
                array('host' => 'foo'),
                false,
                'UnexpectedValueException',
                "Missing 'port' as option"
            ),
            array(
                array('host' => 'foo', 'port' => 'foo'),
                false,
                'UnexpectedValueException',
                "Missing 'username' as option"
            ),
            array(
                array('host' => 'foo', 'port' => 'foo', 'username' => 'foo'),
                false,
                'UnexpectedValueException',
                "Missing 'password' as option"
            ),
            array(
                array('host' => 'foo', 'port' => 'foo', 'username' => 'foo', 'password' => 'foo'),
                array('host' => 'foo', 'port' => 'foo', 'username' => 'foo', 'password' => 'foo'),
            ),
        );
    }
}

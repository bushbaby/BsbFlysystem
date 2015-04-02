<?php

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\SftpAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;

class SftpAdapterFactoryTest extends TestCase
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
        $class          = new \ReflectionClass('BsbFlysystem\Adapter\Factory\SftpAdapterFactory');
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService()
    {
        $sm      = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $factory = new SftpAdapterFactory();

        $adapter = $factory->createService($manager, 'sftpdefault', 'sftp_default');

        $this->assertInstanceOf('League\Flysystem\Sftp\SftpAdapter', $adapter);
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
        $factory = new SftpAdapterFactory($options);

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
                "Missing 'host' as option"
            ],
            [
                ['host' => 'foo'],
                false,
                'UnexpectedValueException',
                "Missing 'port' as option"
            ],
            [
                ['host' => 'foo', 'port' => 'foo'],
                false,
                'UnexpectedValueException',
                "Missing 'username' as option"
            ],
            [
                ['host' => 'foo', 'port' => 'foo', 'username' => 'foo'],
                false,
                'UnexpectedValueException',
                "Missing either 'password' or 'privateKey' as option"
            ],
            [
                ['host' => 'foo', 'port' => 'foo', 'username' => 'foo', 'password' => 'foo'],
                ['host' => 'foo', 'port' => 'foo', 'username' => 'foo', 'password' => 'foo'],
            ],
            [
                ['host' => 'foo', 'port' => 'foo', 'username' => 'foo', 'privateKey' => 'foo'],
                ['host' => 'foo', 'port' => 'foo', 'username' => 'foo', 'privateKey' => 'foo'],
            ],
        ];
    }
}

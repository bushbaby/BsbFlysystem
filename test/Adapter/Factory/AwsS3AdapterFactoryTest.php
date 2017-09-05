<?php

declare(strict_types=1);

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\AwsS3AdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;

class AwsS3AdapterFactoryTest extends TestCase
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
        $class          = new \ReflectionClass('BsbFlysystem\Adapter\Factory\AwsS3AdapterFactory');
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService()
    {
        $this->markTestSkipped('Skipped because Aws3Sv2 and Aws3Sv3 are not compatible.');

        $sm      = Bootstrap::getServiceManager();
        $factory = new AwsS3AdapterFactory();

        $adapter = $factory($sm, 'awss3_default');

        $this->assertInstanceOf('League\Flysystem\AwsS3v2\AwsS3Adapter', $adapter);
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
        $factory = new AwsS3AdapterFactory($options);

        if ($expectedException) {
            $this->expectException($expectedException, $expectedExceptionMessage);
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
                "Missing 'key' as option",
            ],
            [
                ['key' => 'foo'],
                [],
                'UnexpectedValueException',
                "Missing 'secret' as option",
            ],
            [
                [
                    'key'    => 'foo',
                    'secret' => 'secret',
                ],
                [],
                'UnexpectedValueException',
                "Missing 'region' as option",
            ],
            [
                [
                    'key'    => 'foo',
                    'secret' => 'secret',
                    'region' => 'region',
                ],
                [],
                'UnexpectedValueException',
                "Missing 'bucket' as option",
            ],
            [
                [
                    'key'    => 'abc',
                    'secret' => 'def',
                    'region' => 'ghi',
                    'bucket' => 'jkl',
                ],
                [
                    'key'             => 'abc',
                    'secret'          => 'def',
                    'region'          => 'ghi',
                    'bucket'          => 'jkl',
                    'prefix'          => null,
                    'request.options' => [],
                ],
            ],
        ];
    }
}

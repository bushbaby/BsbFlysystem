<?php

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\AwsS3v3AdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;

/**
 * Class AwsS3v3AdapterFactoryTest
 *
 * @package     BsbFlysystemTest\Adapter\Factory
 * @version     1.0
 * @author      Julien Guittard <julien.guittard@mme.com>
 */
class AwsS3v3AdapterFactoryTest extends TestCase
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
        $class          = new \ReflectionClass('BsbFlysystem\Adapter\Factory\AwsS3v3AdapterFactory');
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService()
    {
        $this->markTestSkipped('Skipped because Aws3Sv2 and Aws3Sv3 are not compatible.');

        $sm      = Bootstrap::getServiceManager();
        $factory = new AwsS3v3AdapterFactory();

        $adapter = $factory($sm, 'awss3_default');

        $this->assertInstanceOf('League\Flysystem\AwsS3v3\AwsS3v3Adapter', $adapter);
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
        //var_dump($options);exit;
        $factory = new AwsS3v3AdapterFactory($options);

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
                "Missing 'key' as option"
            ], // #0
            [
                ['key' => 'foo'],
                [],
                'UnexpectedValueException',
                "Missing 'secret' as option"
            ], // #1
            [
                [
                    'key'    => 'foo',
                    'secret' => 'secret',
                ],
                [],
                'UnexpectedValueException',
                "Missing 'region' as option"
            ], // #2
            [
                [
                    'key'    => 'foo',
                    'secret' => 'secret',
                    'region' => 'region',
                ],
                [],
                'UnexpectedValueException',
                "Missing 'bucket' as option"
            ], // #3
            [
                [
                    'key'    => 'abc',
                    'secret' => 'def',
                    'region' => 'ghi',
                    'bucket' => 'jkl',
                ],
                [
                    'key'    => 'abc',
                    'secret' => 'def',
                    'region' => 'ghi',
                    'bucket' => 'jkl',
                    'prefix' => null,
                    'request.options' => []
                ]
            ], // #4
            [
                [
                    'iam' => true,
                    'region' => 'region',
                    'bucket' => 'bucket',
                ],
                [],
            ], // #5
            [
                [
                    'iam' => false,
                    'key'    => 'foo',
                    'region' => 'region',
                    'bucket' => 'bucket',
                ],
                [],
                'UnexpectedValueException',
                "Missing 'region' as option"
            ], // #6
        ];
    }
}
<?php

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\AzureAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;

class AzureAdapterFactoryTest extends TestCase
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
        $class = new \ReflectionClass('BsbFlysystem\Adapter\Factory\AzureAdapterFactory');
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService()
    {
        $this->markTestSkipped("Skipped due to https://github.com/thephpleague/flysystem-azure/pull/16");
        
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $factory = new AzureAdapterFactory(
            [
                'account-name' => 'foo',
                'account-key' => 'bar',
                'container' => 'container',
            ]
        );

        $adapter = $factory->createService($manager, 'azuredefault', 'azure_default');

        $this->assertInstanceOf('League\Flysystem\Azure\AzureAdapter', $adapter);
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
        $factory = new AzureAdapterFactory($options);

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
                "Missing 'account-name' as option"
            ],
            [
                ['account-name' => 'foo'],
                [],
                'UnexpectedValueException',
                "Missing 'account-key' as option"
            ],
            [
                [
                    'account-name' => 'foo',
                    'account-key' => 'bar',
                ],
                [],
                'UnexpectedValueException',
                "Missing 'container' as option"
            ],
            [
                [
                    'account-name' => 'foo',
                    'account-key' => 'bar',
                    'container' => 'container',
                ],
                [
                    'account-name' => 'foo',
                    'account-key' => 'bar',
                    'container' => 'container',
                ]
            ],
        ];
    }
}

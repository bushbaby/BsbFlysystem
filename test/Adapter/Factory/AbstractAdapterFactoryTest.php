<?php

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystemTest\Assets\SimpleAdapterFactory;
use BsbFlysystemTest\Framework\TestCase;
use Zend\ServiceManager\ServiceManager;

class AbstractAdapterFactoryTest extends TestCase
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
        $class          = new \ReflectionClass('BsbFlysystemTest\Assets\SimpleAdapterFactory');
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('mergeMvcConfig');
        $this->method->setAccessible(true);
    }

    public function testOptionsViaContructor()
    {
        $options = ['option' => 1];
        $factory = new SimpleAdapterFactory($options);

        $expected = $options;

        $this->assertEquals($expected, $this->property->getValue($factory));
    }

    public function testOptionsFromConfigService()
    {
        $options = ['option' => 1];
        $factory = new SimpleAdapterFactory();
        $sm      = new ServiceManager();
        $sm->setService(
            'Config',
            [
                'bsb_flysystem' => [
                    'adapters' => [
                        'simple_default' => [
                            'options' => $options
                        ]
                    ]
                ]
            ]
        );

        $this->method->invokeArgs($factory, [$sm, 'simple_default']);
        $expected = $options;

        $this->assertEquals($expected, $this->property->getValue($factory));
    }

    public function testConstructOptionsOverridesOptionsFromConfigService()
    {
        $constructor_options = ['option' => 1, 'option2' => 2];
        $config_options      = ['option' => 0, 'option3' => 3];
        $factory             = new SimpleAdapterFactory($constructor_options);
        $sm                  = new ServiceManager();
        $sm->setService(
            'Config',
            [
                'bsb_flysystem' => [
                    'adapters' => ['simple_default' => ['options' => $config_options]]
                ]
            ]
        );

        $this->method->invokeArgs($factory, [$sm, 'simple_default']);

        $expected = ['option' => 1, 'option2' => 2, 'option3' => 3];

        $this->assertEquals($expected, $this->property->getValue($factory));
    }

    public function testIncompleteConfigPathsDoesNotChangeOptions()
    {
        $constructor_options = ['option' => 1, 'option2' => 2];
        $factory             = new SimpleAdapterFactory($constructor_options);
        $sm                  = new ServiceManager();
        $sm->setService('Config', []);

        $this->method->invokeArgs($factory, [$sm, 'simple_default']);
        $expected = $constructor_options;
        $this->assertEquals($expected, $this->property->getValue($factory));

        $sm = new ServiceManager();

        $this->method->invokeArgs($factory, [$sm, 'simple_default']);
        $expected = $constructor_options;
        $this->assertEquals($expected, $this->property->getValue($factory));

        $sm = new ServiceManager();
        $sm->setService('Config', ['bsb_flysystem' => []]);

        $this->method->invokeArgs($factory, [$sm, 'simple_default']);
        $expected = $constructor_options;
        $this->assertEquals($expected, $this->property->getValue($factory));

        $sm = new ServiceManager();
        $sm->setService('Config', ['bsb_flysystem' => ['adapters' => []]]);

        $this->method->invokeArgs($factory, [$sm, 'simple.simple_default']);
        $expected = $constructor_options;
        $this->assertEquals($expected, $this->property->getValue($factory));

        $sm = new ServiceManager();
        $sm->setService(
            'Config',
            [
                'bsb_flysystem' => [
                    'adapters' => [
                        'simple_default' => []
                    ]
                ]
            ]
        );

        $this->method->invokeArgs($factory, [$sm, 'simple_default']);
        $expected = $constructor_options;
        $this->assertEquals($expected, $this->property->getValue($factory));
    }
}

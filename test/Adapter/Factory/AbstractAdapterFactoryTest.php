<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2020 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystemTest\Assets\SimpleAdapterFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class AbstractAdapterFactoryTest extends TestCase
{
    /**
     * @var ReflectionProperty
     */
    protected $property;

    /**
     * @var ReflectionMethod
     */
    protected $method;

    public function setup(): void
    {
        $class = new ReflectionClass(SimpleAdapterFactory::class);
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('mergeMvcConfig');
        $this->method->setAccessible(true);
    }

    public function testOptionsViaContructor(): void
    {
        $options = ['option' => 1];
        $factory = new SimpleAdapterFactory($options);

        $expected = $options;

        $this->assertEquals($expected, $this->property->getValue($factory));
    }

    public function testOptionsFromConfigService(): void
    {
        $options = ['option' => 1];
        $factory = new SimpleAdapterFactory();
        $sm = new ServiceManager();
        $sm->setService(
            'config',
            [
                'bsb_flysystem' => [
                    'adapters' => [
                        'simple_default' => [
                            'options' => $options,
                        ],
                    ],
                ],
            ]
        );

        $this->method->invokeArgs($factory, [$sm, 'simple_default']);
        $expected = $options;

        $this->assertEquals($expected, $this->property->getValue($factory));
    }

    public function testConstructOptionsOverridesOptionsFromConfigService(): void
    {
        $constructor_options = ['option' => 1, 'option2' => 2];
        $config_options = ['option' => 0, 'option3' => 3];
        $factory = new SimpleAdapterFactory($constructor_options);
        $sm = new ServiceManager();
        $sm->setService(
            'config',
            [
                'bsb_flysystem' => [
                    'adapters' => ['simple_default' => ['options' => $config_options]],
                ],
            ]
        );

        $this->method->invokeArgs($factory, [$sm, 'simple_default']);

        $expected = ['option' => 1, 'option2' => 2, 'option3' => 3];

        $this->assertEquals($expected, $this->property->getValue($factory));
    }

    public function testIncompleteConfigPathsDoesNotChangeOptions(): void
    {
        $constructor_options = ['option' => 1, 'option2' => 2];
        $factory = new SimpleAdapterFactory($constructor_options);
        $sm = new ServiceManager();
        $sm->setService('config', []);

        $this->method->invokeArgs($factory, [$sm, 'simple_default']);
        $expected = $constructor_options;
        $this->assertEquals($expected, $this->property->getValue($factory));

        $sm = new ServiceManager();

        $this->method->invokeArgs($factory, [$sm, 'simple_default']);
        $expected = $constructor_options;
        $this->assertEquals($expected, $this->property->getValue($factory));

        $sm = new ServiceManager();
        $sm->setService('config', ['bsb_flysystem' => []]);

        $this->method->invokeArgs($factory, [$sm, 'simple_default']);
        $expected = $constructor_options;
        $this->assertEquals($expected, $this->property->getValue($factory));

        $sm = new ServiceManager();
        $sm->setService('config', ['bsb_flysystem' => ['adapters' => []]]);

        $this->method->invokeArgs($factory, [$sm, 'simple.simple_default']);
        $expected = $constructor_options;
        $this->assertEquals($expected, $this->property->getValue($factory));

        $sm = new ServiceManager();
        $sm->setService(
            'config',
            [
                'bsb_flysystem' => [
                    'adapters' => [
                        'simple_default' => [],
                    ],
                ],
            ]
        );

        $this->method->invokeArgs($factory, [$sm, 'simple_default']);
        $expected = $constructor_options;
        $this->assertEquals($expected, $this->property->getValue($factory));
    }
}

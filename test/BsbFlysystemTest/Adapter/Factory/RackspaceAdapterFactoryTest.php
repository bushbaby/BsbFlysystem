<?php

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\RackspaceAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;

class RackspaceAdapterFactoryTest extends TestCase
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
        $class          = new \ReflectionClass('BsbFlysystem\Adapter\Factory\RackspaceAdapterFactory');
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService()
    {
        $sm      = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $factory = new RackspaceAdapterFactory();

        $adapter = $factory->createService($manager, 'rackspacedefault', 'rackspace_default');

        $this->assertInstanceOf('League\Flysystem\Adapter\RackSpace', $adapter);
    }

    /**
     * @dataProvider validateConfigProvider
     * @param      $options
     * @param bool $expectedOptions
     * @param bool $expectedException
     * @param bool $expectedExceptionMessage
     */
    public function testValidateConfig(
        $options,
        $expectedOptions = false,
        $expectedException = false,
        $expectedExceptionMessage = false
    ) {
        $factory = new RackspaceAdapterFactory($options);

        if ($expectedException) {
            $this->setExpectedException($expectedException, $expectedExceptionMessage);
        }

        $this->method->invokeArgs($factory, array());

        if (is_array($expectedOptions)) {
            $this->assertEquals($expectedOptions, $this->property->getValue($factory));
        }
    }

    /**
     * @return array
     */
    public function validateConfigProvider()
    {
        return array(
            array(
                array(),
                array(),
                'UnexpectedValueException',
                "Missing 'url' as option"
            ),
            array(
                array('url' => 'some_url'),
                array(),
                'UnexpectedValueException',
                "Missing 'secret' as option"
            ),
            array(
                array(
                    'url'    => 'some_url',
                    'secret' => 'secret'
                ),
                array(),
                'UnexpectedValueException',
                "Missing 'secret' as option"
            ),
            array(
                array(
                    'url'    => 'some_url',
                    'secret' => array(),
                ),
                array(),
                'UnexpectedValueException',
                "Missing 'objectstore' as option"
            ),
            array(
                array(
                    'url'         => 'some_url',
                    'secret'      => array(
                        'username'    => 'foo',
                        'password'    => 'foo',
                        'tenant_name' => 'foo'
                    ),
                    'objectstore' => 'bar'
                ),
                array(),
                'UnexpectedValueException',
                "Missing 'objectstore' as option"
            ),
            array(
                array(
                    'url'         => 'some_url',
                    'secret'      => array(
                        'username'    => 'foo',
                        'password'    => 'foo',
                        'tenant_name' => 'foo'
                    ),
                    'objectstore' => array()
                ),
                array(),
                'UnexpectedValueException',
                "Missing 'objectstore.name' as option"
            ),
            array(
                array(
                    'url'         => 'some_url',
                    'secret'      => array(
                        'username'    => 'foo',
                        'password'    => 'foo',
                        'tenant_name' => 'foo'
                    ),
                    'objectstore' => array(
                        'name' => 'foo',
                    )
                ),
                array(),
                'UnexpectedValueException',
                "Missing 'objectstore.region' as option"
            ),
            array(
                array(
                    'url'         => 'some_url',
                    'secret'      => array(),
                    'objectstore' => array(
                        'name'   => 'foo',
                        'region' => 'foo',
                    )
                ),
                array(),
                'UnexpectedValueException',
                "Missing 'objectstore.container' as option"
            ),
            array(
                array(
                    'url'         => 'some_url',
                    'secret'      => array(),
                    'objectstore' => array(
                        'name'      => 'foo',
                        'region'    => 'foo',
                        'container' => 'foo',
                    )
                ),
                array(
                    'url'         => 'some_url',
                    'secret'      => array(),
                    'objectstore' => array(
                        'name'      => 'foo',
                        'region'    => 'foo',
                        'container' => 'foo',
                        'url_type'  => null, // added
                    ),
                    'options'     => array(), // added
                    'prefix'      => null, // added
                ),
            ),
        );
    }
}

<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2021 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\WebDAVAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use League\Flysystem\WebDAV\WebDAVAdapter;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class WebDAVAdapterFactoryTest extends TestCase
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
        $class = new ReflectionClass(WebDAVAdapterFactory::class);
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService(): void
    {
        $sm = Bootstrap::getServiceManager();
        $factory = new WebDAVAdapterFactory();

        $adapter = $factory($sm, 'webdav_default');

        $this->assertInstanceOf(WebDAVAdapter::class, $adapter);
    }

    /**
     * @dataProvider validateConfigProvider
     */
    public function testValidateConfig(
        array $options,
        ?array $expectedOptions,
        ?string $expectedException,
        ?string $expectedExceptionMessage
    ): void {
        $factory = new WebDAVAdapterFactory($options);

        if ($expectedException) {
            $this->expectException($expectedException);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $this->method->invokeArgs($factory, []);

        if (\is_array($expectedOptions)) {
            $this->assertEquals($expectedOptions, $this->property->getValue($factory));
        }
    }

    public function validateConfigProvider(): array
    {
        return [
            [
                [],
                null,
                'UnexpectedValueException',
                "Missing 'baseUri' as option",
            ],
            [
                ['baseUri' => 'foo'],
                ['baseUri' => 'foo', 'prefix' => null],
                null,
                null,
            ],
        ];
    }
}

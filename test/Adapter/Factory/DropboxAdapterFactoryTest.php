<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2019 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\DropboxAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxAdapterFactoryTest extends TestCase
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
        $class = new ReflectionClass(DropboxAdapterFactory::class);
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService(): void
    {
        $sm = Bootstrap::getServiceManager();
        $factory = new DropboxAdapterFactory();

        $adapter = $factory($sm, 'dropbox_default');

        $this->assertInstanceOf(DropboxAdapter::class, $adapter);
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
        $factory = new DropboxAdapterFactory($options);

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
                [],
                'UnexpectedValueException',
                "Missing 'access_token' as option",
            ],
            [
                ['access_token' => 'foo'],
                ['access_token' => 'foo', 'prefix' => null],
                null,
                null,
            ],
        ];
    }
}

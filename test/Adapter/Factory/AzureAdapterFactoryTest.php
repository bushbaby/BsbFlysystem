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

use BsbFlysystem\Adapter\Factory\AzureAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use League\Flysystem\Azure\AzureAdapter;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class AzureAdapterFactoryTest extends TestCase
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
        $class = new ReflectionClass(AzureAdapterFactory::class);
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService(): void
    {
        $this->markTestSkipped('Skipped due to https://github.com/thephpleague/flysystem-azure/pull/16');

        $sm = Bootstrap::getServiceManager();
        $factory = new AzureAdapterFactory(
            [
                'account-name' => 'foo',
                'account-key' => 'bar',
                'container' => 'container',
            ]
        );

        $adapter = $factory($sm, 'azure_default');

        $this->assertInstanceOf(AzureAdapter::class, $adapter);
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
        $factory = new AzureAdapterFactory($options);

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
                "Missing 'account-name' as option",
            ],
            [
                ['account-name' => 'foo'],
                [],
                'UnexpectedValueException',
                "Missing 'account-key' as option",
            ],
            [
                [
                    'account-name' => 'foo',
                    'account-key' => 'bar',
                ],
                [],
                'UnexpectedValueException',
                "Missing 'container' as option",
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
                ],
                null,
                null,
            ],
        ];
    }
}

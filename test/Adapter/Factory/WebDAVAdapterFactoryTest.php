<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\WebDAVAdapterFactory;
use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\WebDAV\WebDAVAdapter;
use phpmock\phpunit\PHPMock;
use Psr\Container\ContainerInterface;

class WebDAVAdapterFactoryTest extends BaseAdapterFactory
{
    use PHPMock;

    public function testClassExists(): void
    {
        $classExists = $this->getFunctionMock('BsbFlysystem\Adapter\Factory', 'class_exists');
        $classExists->expects($this->once())->willReturn(false);

        $factory = new WebDAVAdapterFactory();
        $container = $this->prophet->prophesize(ContainerInterface::class);

        $this->expectException(RequirementsException::class);
        $factory->doCreateService($container->reveal());
    }

    public function testGettingFromServiceManager(): void
    {
        $factory = new WebDAVAdapterFactory();

        $container = $this->prophet->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);

        $adapter = $factory($container->reveal(), 'webdav_default', [
            'client' => [
                'baseUri' => 'a-base-url',
            ],
        ]);

        $this->assertInstanceOf(WebDAVAdapter::class, $adapter);
    }
}

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

use BsbFlysystem\Adapter\Factory\FtpAdapterFactory;
use League\Flysystem\Ftp\ConnectivityChecker;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionProvider;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use League\MimeTypeDetection\MimeTypeDetector;
use Psr\Container\ContainerInterface;

class FtpAdapterFactoryTest extends BaseAdapterFactory
{
    public function testGettingFromServiceManager(): void
    {
        $factory = new FtpAdapterFactory();

        $container = $this->prophet->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);

        $connectionProvider = $this->prophet->prophesize(FtpConnectionProvider::class);
        $container->get('a-connection-provider')->willReturn($connectionProvider->reveal());

        $connectivityChecker = $this->prophet->prophesize(ConnectivityChecker::class);
        $container->get('a-connectivity-checker')->willReturn($connectivityChecker->reveal());

        $mimeTypeDetector = $this->prophet->prophesize(MimeTypeDetector::class);
        $container->get('a-mime-type-detector')->willReturn($mimeTypeDetector->reveal());

        $visibility = $this->prophet->prophesize(VisibilityConverter::class);
        $container->get('a-visibility')->willReturn($visibility->reveal());

        $adapter = $factory($container->reveal(), 'ftp_default', [
            'connectionOptions' => [
                'host' => 'localhost',
                'root' => '/path/to/root',
                'username' => 'username',
                'password' => 'password',
            ],
            'connectionProvider' => 'a-connection-provider',
            'connectivityChecker' => 'a-connectivity-checker',
            'mimeTypeDetector' => 'a-mime-type-detector',
            'visibilityConverter' => 'a-visibility',
        ]);

        $this->assertInstanceOf(FtpAdapter::class, $adapter);
    }
}

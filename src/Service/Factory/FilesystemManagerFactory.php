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

namespace BsbFlysystem\Service\Factory;

use BsbFlysystem\Filesystem\Factory\FilesystemFactory;
use BsbFlysystem\Service\FilesystemManager;
use Psr\Container\ContainerInterface;

class FilesystemManagerFactory
{
    public function createService(ContainerInterface $container): FilesystemManager
    {
        return $this($container, null);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FilesystemManager
    {
        $config = $container->get('config');
        $config = $config['bsb_flysystem']['filesystems'];
        $serviceConfig = [];
        foreach ($config as $key => $filesystems) {
            $serviceConfig['factories'][$key] = FilesystemFactory::class;
            $serviceConfig['shared'][$key] = isset($filesystems['shared']) ? (bool) $filesystems['shared'] : true;
        }

        return new FilesystemManager($container, $serviceConfig);
    }
}

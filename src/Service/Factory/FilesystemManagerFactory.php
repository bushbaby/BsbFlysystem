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

namespace BsbFlysystem\Service\Factory;

use BsbFlysystem\Filesystem\Factory\FilesystemFactory;
use BsbFlysystem\Service\FilesystemManager;
use Psr\Container\ContainerInterface;

class FilesystemManagerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): FilesystemManager
    {
        $filesystems = ($container->has('config') ? $container->get('config') : [])['bsb_flysystem']['filesystems'] ?? [];

        $serviceConfig = array_reduce(array_keys($filesystems), function (array $serviceConfig, string $name) {
            $serviceConfig['factories'][$name] = FilesystemFactory::class;

            return $serviceConfig;
        }, ['factories' => []]);

        return new FilesystemManager($container, $serviceConfig);
    }
}

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

use BsbFlysystem\Service\AdapterManager;
use BsbFlysystem\Service\Factory\AdapterManagerFactory;
use BsbFlysystem\Service\Factory\FilesystemManagerFactory;
use BsbFlysystem\Service\FilesystemManager;

return [
    'bsb_flysystem' => [
        'adapters' => [
            'local_data' => [
                'type' => 'local',
                'options' => [
                    'root' => './data',
                ],
            ],
        ],
        'cache' => [
            'default' => [
                'type' => 'adapter',
                'file' => 'file.cache',
                'ttl' => 300,
            ],
        ],
        'filesystems' => [
            'default' => [
                'adapter' => 'local_data',
            ],
        ],
        'adapter_manager' => [
            'config' => [],
            'lazy_services' => [
                // directory where proxy classes will be written - default to system_get_tmp_dir()
                // 'proxies_target_dir'    => 'data/cache',
                // namespace of the generated proxies, default to "ProxyManagerGeneratedProxy"
                // 'proxies_namespace'     => null,
                // whether the generated proxy classes should be written to disk
                // 'write_proxy_files'     => false,
            ],
        ],
        'filesystem_manager' => [
            'config' => [],
        ],
    ],
    'service_manager' => [
        'aliases' => [
            'BsbFlysystemManager' => FilesystemManager::class,
            'BsbFlysystemAdapterManager' => AdapterManager::class,
        ],
        'factories' => [
            AdapterManager::class => AdapterManagerFactory::class,
            FilesystemManager::class => FilesystemManagerFactory::class,
        ],
    ],
];

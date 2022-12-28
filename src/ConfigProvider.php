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

namespace BsbFlysystem;

use BsbFlysystem\Adapter\Factory\LocalAdapterFactory;
use BsbFlysystem\Service\AdapterManager;
use BsbFlysystem\Service\Factory\AdapterManagerFactory;
use BsbFlysystem\Service\Factory\FilesystemManagerFactory;
use BsbFlysystem\Service\FilesystemManager;

final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'bsb_flysystem' => [
                'adapters' => $this->getAdapterConfig(),
                'filesystems' => $this->getFilesystemsConfig(),
                'adapter_manager' => $this->getAdapterManagerConfig(),
                'filesystem_manager' => $this->getFilesystemManagerConfig(),
            ],
        ];
    }

    public function getDependencyConfig(): array
    {
        return [
            'aliases' => [
                'BsbFlysystemManager' => FilesystemManager::class,
                'BsbFlysystemAdapterManager' => AdapterManager::class,
            ],
            'factories' => [
                AdapterManager::class => AdapterManagerFactory::class,
                FilesystemManager::class => FilesystemManagerFactory::class,
            ],
        ];
    }

    public function getAdapterConfig(): array
    {
        return [
            'local_data' => [
                'factory' => LocalAdapterFactory::class,
                'options' => [
                    'location' => './data',
                    'lazyRootCreation' => true,
                ],
            ],
        ];
    }

    public function getFilesystemsConfig(): array
    {
        return [
            'default' => [
                'adapter' => 'local_data',
            ],
        ];
    }

    public function getAdapterManagerConfig(): array
    {
        return [
            'config' => [],
            'lazy_services' => [
                // directory where proxy classes will be written - default to system_get_tmp_dir()
                // 'proxies_target_dir'    => 'data/cache',
                // namespace of the generated proxies, default to "ProxyManagerGeneratedProxy"
                // 'proxies_namespace'     => null,
                // whether the generated proxy classes should be written to disk
                // 'write_proxy_files'     => false,
            ],
        ];
    }

    public function getFilesystemManagerConfig(): array
    {
        return [
            'config' => [],
        ];
    }
}

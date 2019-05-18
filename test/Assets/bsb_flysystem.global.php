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

return [
    'bsb_flysystem' => [
        'adapters' => [
            'local_default' => [
                'type' => 'local',
                'options' => [
                    'root' => './test/_build/files',
                ],
            ],
            'local_default_unshared' => [
                'type' => 'local',
                'shared' => false,
                'options' => [
                    'root' => './test/_build/files',
                ],
            ],
            'null_default' => [
                'type' => 'null',
                'options' => [],
            ],
            'sftp_default' => [
                'type' => 'sftp',
                'options' => [
                    'host' => 'xxxxx',
                    'port' => 22,
                    'username' => 'xxxxx',
                    'password' => 'xxxxx',
                    'timeout' => 10,
                ],
            ],
            'ftp_default' => [
                'type' => 'ftp',
                'options' => [
                    'host' => 'xxxxx',
                    'username' => 'xxxxx',
                    'password' => 'xxxxx',
                    /* optional config settings */
                    'port' => 21,
                    'root' => '/',
                    'passive' => true,
                    'ssl' => false,
                    'timeout' => 30,
                ],
            ],
            'ftpd_default' => [
                'type' => 'ftpd',
                'options' => [
                    'host' => 'xxxxx',
                    'username' => 'xxxxx',
                    'password' => 'xxxxx',
                    /* optional config settings */
                    'port' => 21,
                    'root' => '/',
                    'passive' => true,
                    'ssl' => false,
                    'timeout' => 30,
                ],
            ],
            'googleclouddrive_default' => [
                'type' => 'ftpd',
                'options' => [
                    'project_id' => 'xxxxx',
                    'bucket' => 'xxxxx',
                ],
            ],
            'zip_default' => [
                'type' => 'ziparchive',
                'options' => [
                    'archive' => './test/_build/files.zip',
                ],
            ],
            'rackspace_default' => [
                'type' => 'rackspace',
                'options' => [
                    'url' => 'http.xxxxx.xxx',
                    'secret' => [
                        'username' => 'xxxxx',
                        'password' => 'xxxxx',
                        'tenant_name' => 'xxxxx',
                    ],
                    'objectstore' => [
                        'name' => 'xxxxx',
                        'region' => 'XX',
                        'url_type' => 'publicURL',
                        'container' => 'xxxxx',
                    ],
                ],
            ],
            'rackspace_lazy' => [
                'type' => 'rackspace',
                'options' => [
                    'url' => 'http.xxxxx.xxx',
                    'secret' => [
                        'username' => 'xxxxx',
                        'password' => 'xxxxx',
                        'tenant_name' => 'xxxxx',
                    ],
                    'objectstore' => [
                        'name' => 'xxxxx',
                        'region' => 'XX',
                        'url_type' => 'publicURL',
                        'container' => 'xxxxx',
                    ],
                ],
            ],
            'dropbox_default' => [
                'type' => 'dropbox',
                'shared' => 'off', /* optional */
                'options' => [
                    'access_token' => 'xxxxx',
                ],
            ],
            'awss3v3_default' => [
                'type' => 'awss3v3',
                'options' => [
                    'credentials' => [
                        'key' => 'your-app-id',
                        'secret' => 'xxxxx',
                    ],
                    'region' => 'eu-west-1',
                    'bucket' => 'xxxxx',
                    'version' => 'latest', // default: 'latest'
                    'request.options' => [], // Guzzle request options; see http://docs.guzzlephp.org/en/latest/request-options.html#proxy
                ],
            ],
            'replicate_default' => [
                'type' => 'replicate',
                'options' => [
                    'source' => 'local_default',
                    'replicate' => 'zip_default',
                ],
            ],
            'webdav_default' => [
                'type' => 'webdav',
                'options' => [
                    'baseUri' => 'http.xxxxx.xxx',
                    'userName' => 'xxxxx',
                    'password' => 'xxxxx',
                ],
            ],
        ],
        'filesystems' => [
            'default' => [
                'adapter' => 'local_default',
            ],
            'default_unshared' => [
                'shared' => false,
                'adapter' => 'local_default_unshared',
            ],
            'default_cached' => [
                'adapter' => 'local_default',
                'cache' => 'Cache\BsbFlysystem\Memory',
            ],
        ],
        'adapter_manager' => [],
    ],
    'caches' => [
        'Cache\BsbFlysystem\Memory' => [
            'adapter' => [
                'name' => 'memory',
                'options' => [
                    'ttl' => 5,
                    'namespace' => 'bsbflystem',
                ],
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
        ],
    ],
];

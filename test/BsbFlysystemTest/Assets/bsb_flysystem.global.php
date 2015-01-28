<?php

return [
    'bsb_flysystem'   => [
        'adapters'        => [
            'local_default'          => [
                'type'    => 'local',
                'options' => [
                    'root' => './test/build/files'
                ],
            ],
            'local_default_unshared' => [
                'type'    => 'local',
                'shared'  => false,
                'options' => [
                    'root' => './test/build/files'
                ],
            ],
            'copy_default'           => [
                'type'    => 'copy',
                'options' => [
                    'consumer_key'    => 'xxxxx',
                    'consumer_secret' => 'xxxxx',
                    'access_token'    => 'xxxxx',
                    'token_secret'    => 'xxxxx',
                ],
            ],
            'null_default'           => [
                'type'    => 'null',
                'options' => [],
            ],
            'sftp_default'           => [
                'type'    => 'sftp',
                'options' => [
                    'host'     => 'xxxxx',
                    'port'     => 22,
                    'username' => 'xxxxx',
                    'password' => 'xxxxx',
                    'timeout'  => 10,
                ],
            ],
            'ftp_default'            => [
                'type'    => 'ftp',
                'options' => [
                    'host'     => 'xxxxx',
                    'username' => 'xxxxx',
                    'password' => 'xxxxx',
                    /** optional config settings */
                    'port'     => 21,
                    'root'     => '/',
                    'passive'  => true,
                    'ssl'      => false,
                    'timeout'  => 30,
                ],
            ],
            'zip_default'            => [
                'type'    => 'zip',
                'options' => [
                    'archive' => './test/build/files.zip'

                ],
            ],
            'rackspace_default'      => [
                'type'    => 'rackspace',
                'options' => [
                    'url'         => "http.xxxxx.xxx",
                    'secret'      => [
                        'username'    => "xxxxx",
                        'password'    => "xxxxx",
                        'tenant_name' => "xxxxx"
                    ],
                    'objectstore' => [
                        'name'      => 'xxxxx',
                        'region'    => 'XX',
                        'url_type'  => 'publicURL',
                        'container' => 'xxxxx'
                    ],
                ],
            ],
            'rackspace_lazy'         => [
                'type'    => 'rackspace',
                'options' => [
                    'url'         => "http.xxxxx.xxx",
                    'secret'      => [
                        'username'    => "xxxxx",
                        'password'    => "xxxxx",
                        'tenant_name' => "xxxxx"
                    ],
                    'objectstore' => [
                        'name'      => 'xxxxx',
                        'region'    => 'XX',
                        'url_type'  => 'publicURL',
                        'container' => 'xxxxx'
                    ],
                ],
            ],
            'dropbox_default'        => [
                'type'    => 'dropbox',
                'shared'  => 'off', /* optional */
                'options' => [
                    'client_identifier' => 'xxxxx',
                    'access_token'      => 'xxxxx'
                ],
            ],
            'awss3_default'          => [
                'type'    => 'awss3',
                'options' => [
                    'key'    => 'xxxxx',
                    'secret' => 'xxxxx',
                    'region' => 'eu-west-1',
                    'bucket' => 'xxxxx'
                ],
            ],
            'replicate_default'      => [
                'type'    => 'replicate',
                'options' => [
                    'source'    => 'local_default',
                    'replicate' => 'zip_default'
                ],
            ],
            'webdav_default'         => [
                'type'    => 'webdav',
                'options' => [
                    'baseUri'  => 'http.xxxxx.xxx',
                    'userName' => 'xxxxx',
                    'password' => 'xxxxx'
                ],
            ],
        ],
        'filesystems'     => [
            'default'          => [
                'adapter' => 'local_default',
            ],
            'default_unshared' => [
                'shared'  => false,
                'adapter' => 'local_default_unshared',
            ],
            'default_cached'   => [
                'adapter' => 'local_default',
                'cache'   => 'Cache\BsbFlysystem\Memory'
            ],
        ],
        'adapter_manager' => [],
    ],
    'caches'          => [
        'Cache\BsbFlysystem\Memory' => [
            'adapter' => [
                'name'    => 'memory',
                'options' => [
                    'ttl'       => 5,
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

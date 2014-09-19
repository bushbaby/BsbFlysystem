<?php

return array(
    'bsb_flysystem' => array(
        'adapters'        => array(
            'local_default'       => array(
                'type'    => 'local',
                'options' => array(
                    'root' => './test/build/files'
                ),
            ),
            'local_data_unshared' => array(
                'type'    => 'local',
                'shared'  => false,
                'options' => array(
                    'root' => './test/build/files'
                ),
            ),
            'copy_default'        => array(
                'type'    => 'copy',
                'options' => array(
                    'consumer_key'    => 'xxxxx',
                    'consumer_secret' => 'xxxxx',
                    'access_token'    => 'xxxxx',
                    'token_secret'    => 'xxxxx',
                ),
            ),
            'null_default'        => array(
                'type'    => 'null',
                'options' => array(),
            ),
            'sftp_default'        => array(
                'type'    => 'sftp',
                'options' => array(
                    'host'     => 'xxxxx',
                    'port'     => 22,
                    'username' => 'xxxxx',
                    'password' => 'xxxxx',
                    'timeout'  => 10,
                ),
            ),
            'ftp_default'         => array(
                'type'    => 'ftp',
                'options' => array(
                    'host'     => 'xxxxx',
                    'username' => 'xxxxx',
                    'password' => 'xxxxx',
                    /** optional config settings */
                    'port'     => 21,
                    'root'     => '/',
                    'passive'  => true,
                    'ssl'      => false,
                    'timeout'  => 30,
                ),
            ),
            'zip_default'         => array(
                'type'    => 'zip',
                'options' => array(
                    'archive' => './test/build/files.zip'

                ),
            ),
            'rackspace_default'   => array(
                'type'    => 'rackspace',
                'options' => array(
                    'url'         => "http.xxxxx.xxx",
                    'secret'      => array(
                        'username'    => "xxxxx",
                        'password'    => "xxxxx",
                        'tenant_name' => "xxxxx"
                    ),
                    'objectstore' => array(
                        'name'      => 'xxxxx',
                        'region'    => 'XX',
                        'url_type'  => 'publicURL',
                        'container' => 'xxxxx'
                    ),
                ),
            ),
            'rackspace_lazy'      => array(
                'type'    => 'rackspace',
                'options' => array(
                    'url'         => "http.xxxxx.xxx",
                    'secret'      => array(
                        'username'    => "xxxxx",
                        'password'    => "xxxxx",
                        'tenant_name' => "xxxxx"
                    ),
                    'objectstore' => array(
                        'name'      => 'xxxxx',
                        'region'    => 'XX',
                        'url_type'  => 'publicURL',
                        'container' => 'xxxxx'
                    ),
                ),
            ),
            'dropbox_default'     => array(
                'type'    => 'dropbox',
                'shared'  => 'off', /* optional */
                'options' => array(
                    'client_identifier' => 'xxxxx',
                    'access_token'      => 'xxxxx'
                ),
            ),
            'awss3_default'       => array(
                'type'    => 'awss3',
                'options' => array(
                    'key'    => 'xxxxx',
                    'secret' => 'xxxxx',
                    'region' => 'eu-west-1',
                    'bucket' => 'xxxxx'
                ),
            ),
            'replicate_default'   => array(
                'type'    => 'replicate',
                'options' => array(
                    'source'    => 'local_default',
                    'replicate' => 'zip_default'
                ),
            ),
            'webdav_default'      => array(
                'type'    => 'webdav',
                'options' => array(
                    'baseUri'  => 'http.xxxxx.xxx',
                    'userName' => 'xxxxx',
                    'password' => 'xxxxx'
                ),
            ),
        ),
        'filesystems'     => array(
            'default'          => array(
                'adapter' => 'local_data',
            ),
            'default_unshared' => array(
                'shared'  => false,
                'adapter' => 'local_data_unshared',
            )
        ),
        'adapter_manager' => array(),
    ),
);

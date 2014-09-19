<?php

return array(
    'bsb_flysystem'   => array(
        'adapters'           => array(
            'local_data' => array(
                'type'    => 'local',
                'options' => array(
                    'root' => './data'
                )
            ),
        ),
        'cache'              => array(
            'default' => array(
                'type' => 'adapter',
                'file' => 'file.cache',
                'ttl'  => 300,
            )
        ),
        'filesystems'        => array(
            'default' => array(
                'adapter' => 'local_data',
            )
        ),
        'adapter_manager'    => array(
            'services'      => array(),
            'lazy_services' => array(
                // directory where proxy classes will be written - default to system_get_tmp_dir()
                // 'proxies_target_dir'    => 'data/cache',
                // namespace of the generated proxies, default to "ProxyManagerGeneratedProxy"
                // 'proxies_namespace'     => null,
                // whether the generated proxy classes should be written to disk
                // 'write_proxy_files'     => false,
            ),
        ),
        'filesystem_manager' => array(
            'services' => array(),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'BsbFlysystemAdapterManager' => 'BsbFlysystem\Service\Factory\AdapterManagerFactory',
            'BsbFlysystemManager'        => 'BsbFlysystem\Service\Factory\FilesystemManagerFactory',
        ),
    ),
);

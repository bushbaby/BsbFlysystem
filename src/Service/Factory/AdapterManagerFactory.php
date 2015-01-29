<?php

namespace BsbFlysystem\Service\Factory;

use BsbFlysystem\Service\AdapterManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdapterManagerFactory implements FactoryInterface
{
    protected $adapterFactoryMap = [
        'awss3'      => 'BsbFlysystem\Adapter\Factory\AwsS3AdapterFactory',
        'copy'       => 'BsbFlysystem\Adapter\Factory\CopyAdapterFactory',
        'dropbox'    => 'BsbFlysystem\Adapter\Factory\DropboxAdapterFactory',
        'ftp'        => 'BsbFlysystem\Adapter\Factory\FtpAdapterFactory',
        'local'      => 'BsbFlysystem\Adapter\Factory\LocalAdapterFactory',
        'rackspace'  => 'BsbFlysystem\Adapter\Factory\RackspaceAdapterFactory',
        'replicate'  => 'BsbFlysystem\Adapter\Factory\ReplicateAdapterFactory',
        'sftp'       => 'BsbFlysystem\Adapter\Factory\SftpAdapterFactory',
        'webdav'     => 'BsbFlysystem\Adapter\Factory\WebDavAdapterFactory',
        'ziparchive' => 'BsbFlysystem\Adapter\Factory\ZipArchiveAdapterFactory',
    ];

    protected $adapterInvokableMap = [
        'null'       => 'League\Flysystem\Adapter\NullAdapter',
    ];

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config        = $serviceLocator->get('config');
        $config        = $config['bsb_flysystem'];
        $serviceConfig = $config['adapter_manager']['config'];

        foreach ($config['adapters'] as $name => $adapterConfig) {
            if (!isset($adapterConfig['type'])) {
                throw new \UnexpectedValueException(sprintf(
                    "Missing 'type' key for the adapter '%s' configuration",
                    $name
                ));
            }

            $type = $adapterConfig['type'];

            if (isset($adapterConfig['shared'])) {
                $serviceConfig['shared'][$name] = filter_var($adapterConfig['shared'], FILTER_VALIDATE_BOOLEAN);
            }

            if (isset($this->adapterInvokableMap[$type])) {
                $serviceConfig['invokables'][$name] = $this->adapterInvokableMap[strtolower($type)];
            } elseif (isset($this->adapterFactoryMap[$type])) {
                $serviceConfig['factories'][$name] = $this->adapterFactoryMap[strtolower($type)];
            } else {
                throw new \UnexpectedValueException(sprintf("Unknown adapter 'type' ('%s')", $name));
            }
        }

        $serviceConfig = new Config($serviceConfig);

        return new AdapterManager($serviceConfig);
    }
}

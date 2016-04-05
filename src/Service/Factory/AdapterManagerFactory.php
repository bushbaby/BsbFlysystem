<?php

namespace BsbFlysystem\Service\Factory;

use BsbFlysystem\Service\AdapterManager;
use UnexpectedValueException;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;

class AdapterManagerFactory implements FactoryInterface
{

    /**
     * @var array mapping adapter types to plugin configuration
     */
    protected $adapterMap = [
        'factories' => [
            'awss3'      => 'BsbFlysystem\Adapter\Factory\AwsS3AdapterFactory',
            'awss3v3'    => 'BsbFlysystem\Adapter\Factory\AwsS3v3AdapterFactory',
            'azure'      => 'BsbFlysystem\Adapter\Factory\AzureAdapterFactory',
            'copy'       => 'BsbFlysystem\Adapter\Factory\CopyAdapterFactory',
            'dropbox'    => 'BsbFlysystem\Adapter\Factory\DropboxAdapterFactory',
            'ftp'        => 'BsbFlysystem\Adapter\Factory\FtpAdapterFactory',
            'local'      => 'BsbFlysystem\Adapter\Factory\LocalAdapterFactory',
            'rackspace'  => 'BsbFlysystem\Adapter\Factory\RackspaceAdapterFactory',
            'replicate'  => 'BsbFlysystem\Adapter\Factory\ReplicateAdapterFactory',
            'sftp'       => 'BsbFlysystem\Adapter\Factory\SftpAdapterFactory',
            'webdav'     => 'BsbFlysystem\Adapter\Factory\WebDavAdapterFactory',
            'ziparchive' => 'BsbFlysystem\Adapter\Factory\ZipArchiveAdapterFactory',
            'vfs'        => 'BsbFlysystem\Adapter\Factory\VfsAdapterFactory',
        ],
        'invokables' => [
            'null' => 'League\Flysystem\Adapter\NullAdapter',
        ]
    ];

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return AdapterManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config         = $serviceLocator->get('config');
        $config         = $config['bsb_flysystem'];
        $serviceConfig  = isset($config['adapter_manager']['config']) ? $config['adapter_manager']['config'] : [];
        $adapterMap     = $this->adapterMap;

        if (isset($config['adapter_map'])) {
            $adapterMap = ArrayUtils::merge($this->adapterMap, $config['adapter_map']);
        }

        foreach ($config['adapters'] as $name => $adapterConfig) {
            if (!isset($adapterConfig['type'])) {
                throw new UnexpectedValueException(sprintf(
                    "Missing 'type' key for the adapter '%s' configuration",
                    $name
                ));
            }

            $type = strtolower($adapterConfig['type']);

            foreach (array_keys($adapterMap) as $serviceKind) {
                if (isset($adapterMap[$serviceKind][$type])) {
                    $serviceConfig[$serviceKind][$name] = $adapterMap[$serviceKind][$type];

                    if (isset($adapterConfig['shared'])) {
                        $serviceConfig['shared'][$name] = filter_var($adapterConfig['shared'], FILTER_VALIDATE_BOOLEAN);
                    }

                    continue 2;
                }
            }

            throw new UnexpectedValueException(sprintf("Unknown adapter type '%s'", $type));
        }

        $serviceConfig = new Config($serviceConfig);

        $adapterManager = new AdapterManager($serviceConfig);
        $adapterManager->setServiceLocator($serviceLocator);

        return $adapterManager;
    }
}

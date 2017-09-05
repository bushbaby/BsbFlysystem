<?php

declare(strict_types=1);

namespace BsbFlysystem\Service\Factory;

use BsbFlysystem\Exception\UnexpectedValueException;
use BsbFlysystem\Service\AdapterManager;
use Interop\Container\ContainerInterface;
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
            'awss3v3'    => \BsbFlysystem\Adapter\Factory\AwsS3v3AdapterFactory::class,
            'azure'      => \BsbFlysystem\Adapter\Factory\AzureAdapterFactory::class,
            'dropbox'    => \BsbFlysystem\Adapter\Factory\DropboxAdapterFactory::class,
            'ftp'        => \BsbFlysystem\Adapter\Factory\FtpAdapterFactory::class,
            'local'      => \BsbFlysystem\Adapter\Factory\LocalAdapterFactory::class,
            'rackspace'  => \BsbFlysystem\Adapter\Factory\RackspaceAdapterFactory::class,
            'replicate'  => \BsbFlysystem\Adapter\Factory\ReplicateAdapterFactory::class,
            'sftp'       => \BsbFlysystem\Adapter\Factory\SftpAdapterFactory::class,
            'webdav'     => \BsbFlysystem\Adapter\Factory\WebDAVAdapterFactory::class,
            'ziparchive' => \BsbFlysystem\Adapter\Factory\ZipArchiveAdapterFactory::class,
            'vfs'        => \BsbFlysystem\Adapter\Factory\VfsAdapterFactory::class,
            'null'       => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
        'aliases' => [
            'null' => \League\Flysystem\Adapter\NullAdapter::class,
        ],
    ];

    public function createService(ServiceLocatorInterface $serviceLocator): AdapterManager
    {
        if (method_exists($serviceLocator, 'getServiceLocator')) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        return $this($serviceLocator, null);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AdapterManager
    {
        $config        = $container->get('config');
        $config        = $config['bsb_flysystem'];
        $serviceConfig = $config['adapter_manager']['config'] ?? [];
        $adapterMap    = $this->adapterMap;

        if (isset($config['adapter_map'])) {
            $adapterMap = ArrayUtils::merge($this->adapterMap, $config['adapter_map']);
        }

        foreach ($config['adapters'] as $name => $adapterConfig) {
            if (! isset($adapterConfig['type'])) {
                throw new UnexpectedValueException(sprintf(
                    "Missing 'type' key for the adapter '%s' configuration",
                    $name
                ));
            }

            $type = strtolower($adapterConfig['type']);

            if (! in_array($type, array_keys($adapterMap['factories']), false)) {
                throw new UnexpectedValueException(sprintf("Unknown adapter type '%s'", $type));
            }

            foreach (array_keys($adapterMap) as $serviceKind) {
                if (isset($adapterMap[$serviceKind][$type])) {
                    $serviceConfig[$serviceKind][$name] = $adapterMap[$serviceKind][$type];

                    if (isset($adapterConfig['shared'])) {
                        $serviceConfig['shared'][$name] = filter_var($adapterConfig['shared'], FILTER_VALIDATE_BOOLEAN);
                    }
                }
            }
        }

        return new AdapterManager($container, $serviceConfig);
    }
}

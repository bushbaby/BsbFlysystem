<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2020 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystem\Service\Factory;

use BsbFlysystem\Adapter\Factory\AwsS3v3AdapterFactory;
use BsbFlysystem\Adapter\Factory\AzureAdapterFactory;
use BsbFlysystem\Adapter\Factory\DropboxAdapterFactory;
use BsbFlysystem\Adapter\Factory\FtpAdapterFactory;
use BsbFlysystem\Adapter\Factory\FtpdAdapterFactory;
use BsbFlysystem\Adapter\Factory\GoogleCloudDriveAdapterFactory;
use BsbFlysystem\Adapter\Factory\LocalAdapterFactory;
use BsbFlysystem\Adapter\Factory\RackspaceAdapterFactory;
use BsbFlysystem\Adapter\Factory\ReplicateAdapterFactory;
use BsbFlysystem\Adapter\Factory\SftpAdapterFactory;
use BsbFlysystem\Adapter\Factory\VfsAdapterFactory;
use BsbFlysystem\Adapter\Factory\WebDAVAdapterFactory;
use BsbFlysystem\Adapter\Factory\ZipArchiveAdapterFactory;
use BsbFlysystem\Exception\UnexpectedValueException;
use BsbFlysystem\Service\AdapterManager;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Stdlib\ArrayUtils;
use League\Flysystem\Adapter\NullAdapter;
use Psr\Container\ContainerInterface;

class AdapterManagerFactory
{
    /**
     * @var array mapping adapter types to plugin configuration
     */
    protected $adapterMap = [
        'factories' => [
            'awss3v3' => AwsS3v3AdapterFactory::class,
            'azure' => AzureAdapterFactory::class,
            'dropbox' => DropboxAdapterFactory::class,
            'ftp' => FtpAdapterFactory::class,
            'ftpd' => FtpdAdapterFactory::class,
            'googlecloudstorage' => GoogleCloudDriveAdapterFactory::class,
            'local' => LocalAdapterFactory::class,
            'rackspace' => RackspaceAdapterFactory::class,
            'replicate' => ReplicateAdapterFactory::class,
            'sftp' => SftpAdapterFactory::class,
            'webdav' => WebDAVAdapterFactory::class,
            'ziparchive' => ZipArchiveAdapterFactory::class,
            'vfs' => VfsAdapterFactory::class,
            'null' => InvokableFactory::class,
        ],
        'aliases' => [
            'null' => NullAdapter::class,
        ],
    ];

    public function createService(ContainerInterface $container): AdapterManager
    {
        if (\method_exists($container, 'getServiceLocator')) {
            $container = $container->getServiceLocator();
        }

        return $this($container, null);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AdapterManager
    {
        $config = $container->get('config');
        $config = $config['bsb_flysystem'];
        $serviceConfig = $config['adapter_manager']['config'] ?? [];
        $adapterMap = $this->adapterMap;

        if (isset($config['adapter_map'])) {
            $adapterMap = ArrayUtils::merge($this->adapterMap, $config['adapter_map']);
        }

        foreach ($config['adapters'] as $name => $adapterConfig) {
            if (! isset($adapterConfig['type'])) {
                throw new UnexpectedValueException(\sprintf("Missing 'type' key for the adapter '%s' configuration", $name));
            }

            $type = \strtolower($adapterConfig['type']);

            if (! \in_array($type, \array_keys($adapterMap['factories']), false)) {
                throw new UnexpectedValueException(\sprintf("Unknown adapter type '%s'", $type));
            }

            foreach (\array_keys($adapterMap) as $serviceKind) {
                if (isset($adapterMap[$serviceKind][$type])) {
                    $serviceConfig[$serviceKind][$name] = $adapterMap[$serviceKind][$type];

                    if (isset($adapterConfig['shared'])) {
                        $serviceConfig['shared'][$name] = \filter_var($adapterConfig['shared'], FILTER_VALIDATE_BOOLEAN);
                    }
                }
            }
        }

        return new AdapterManager($container, $serviceConfig);
    }
}

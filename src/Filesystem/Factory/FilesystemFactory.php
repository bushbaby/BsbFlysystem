<?php

declare(strict_types=1);

namespace BsbFlysystem\Filesystem\Factory;

use BsbFlysystem\Cache\ZendStorageCache;
use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use Interop\Container\ContainerInterface;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\CacheInterface;
use League\Flysystem\EventableFilesystem\EventableFilesystem;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Zend\Cache\Storage\StorageInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FilesystemFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $options;

    public function __construct(array $options = [])
    {
        $this->setCreationOptions($options);
    }

    public function setCreationOptions(array $options)
    {
        $this->options = $options;

        if (! isset($this->options['adapter_options'])) {
            $this->options['adapter_options'] = [];
        }
    }

    public function createService(ServiceLocatorInterface $serviceLocator): FilesystemInterface
    {
        if (method_exists($serviceLocator, 'getServiceLocator')) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        $requestedName = func_get_arg(2);

        return $this($serviceLocator, $requestedName);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FilesystemInterface
    {
        $config = $container->get('config');
        $fsConfig = $config['bsb_flysystem']['filesystems'][$requestedName];
        if (! isset($fsConfig['adapter'])) {
            throw new UnexpectedValueException(sprintf(
                "Missing 'adapter' key for the filesystem '%s' configuration",
                $requestedName
            ));
        }

        if (null !== $options) {
            $this->setCreationOptions($options);
        }

        $adapter = $container
            ->get('BsbFlysystemAdapterManager')
            ->get($fsConfig['adapter'], $this->options['adapter_options']);

        $options = isset($fsConfig['options']) && is_array($fsConfig['options']) ? $fsConfig['options'] : [];

        if (isset($fsConfig['cache']) && is_string($fsConfig['cache'])) {
            if (! class_exists(\League\Flysystem\Cached\CachedAdapter::class)) {
                throw new RequirementsException(['league/flysystem-cached-adapter'], 'CachedAdapter');
            }

            $cacheAdapter = $container->get($fsConfig['cache']);

            // wrap if StorageInterface, use filesystem name a key
            if ($cacheAdapter instanceof StorageInterface) {
                $cacheAdapter = new ZendStorageCache($cacheAdapter, $requestedName);
            }

            // ignore if not CacheInterface
            if ($cacheAdapter instanceof CacheInterface) {
                $adapter = new CachedAdapter($adapter, $cacheAdapter);
            }
        }

        if (isset($fsConfig['eventable']) && filter_var($fsConfig['eventable'], FILTER_VALIDATE_BOOLEAN)) {
            if (! class_exists(\League\Flysystem\EventableFilesystem\EventableFilesystem::class)) {
                throw new RequirementsException(['league/flysystem-eventable-filesystem'], 'EventableFilesystem');
            }

            $filesystem = new EventableFilesystem($adapter, $options);
        } else {
            $filesystem = new Filesystem($adapter, $options);
        }

        if (isset($fsConfig['plugins']) && is_array($fsConfig['plugins'])) {
            foreach ($fsConfig['plugins'] as $plugin) {
                $plugin = new $plugin();
                $filesystem->addPlugin($plugin);
            }
        }

        return $filesystem;
    }
}

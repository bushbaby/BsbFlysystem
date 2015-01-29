<?php

namespace BsbFlysystem\Filesystem\Factory;

use BsbFlysystem\Cache\ZendStorageCache;
use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\CacheInterface;
use League\Flysystem\EventableFilesystem\EventableFilesystem;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use UnexpectedValueException;
use Zend\Cache\Storage\StorageInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FilesystemAbstractFactory implements AbstractFactoryInterface, MutableCreationOptionsInterface
{
    /**
     * @var array
     */
    protected $options;

    public function __construct(array $options = [])
    {
        $this->setCreationOptions($options);
    }

    /**
     * Set creation options
     *
     * @param  array $options
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;

        if (!isset($this->options['adapter_options'])) {
            $this->options['adapter_options'] = [];
        }
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        $config         = $serviceLocator->get('config');

        return isset($config['bsb_flysystem']['filesystems'][$requestedName]);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     * @return FilesystemInterface
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        $config         = $serviceLocator->get('config');
        $fsConfig       = $config['bsb_flysystem']['filesystems'][$requestedName];

        if (!isset($fsConfig['adapter'])) {
            throw new UnexpectedValueException(sprintf(
                "Missing 'adapter' key for the filesystem '%s' configuration",
                $name
            ));
        }

        $adapter = $serviceLocator
            ->get('BsbFlysystemAdapterManager')
            ->get($fsConfig['adapter'], $this->options['adapter_options']);

        $options = isset($fsConfig['options']) && is_array($fsConfig['options']) ? $fsConfig['options'] : [];

        if (isset($fsConfig['cache']) && is_string($fsConfig['cache'])) {
            if (!class_exists('League\Flysystem\Cached\CachedAdapter')) {
                throw new RequirementsException(
                    sprintf("Install '%s' to use cached adapters", 'league/flysystem-cached-adapter')
                );
            }

            $cacheAdapter = $serviceLocator
                ->get($fsConfig['cache']);

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
            if (!class_exists('League\Flysystem\EventableFilesystem\EventableFilesystem')) {
                throw new RequirementsException(
                    sprintf("Install '%s' to use EventableFilesystem", 'league/flysystem-eventable-filesystem')
                );
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

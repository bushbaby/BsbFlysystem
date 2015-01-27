<?php

namespace BsbFlysystem\Filesystem\Factory;

use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\EventableFilesystem\EventableFilesystem;
use League\Flysystem\Filesystem;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FilesystemFactory implements FactoryInterface, MutableCreationOptionsInterface
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
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @todo implement caching
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        while (is_callable([$serviceLocator, 'getServiceLocator'])) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        $config = $serviceLocator->get('Config');
        $config = $config['bsb_flysystem']['filesystems'][func_get_arg(2)];

        $adapter = $serviceLocator
            ->get('BsbFlysystemAdapterManager')
            ->get($config['adapter'], $this->options['adapter_options']);

        $options = isset($config['options']) && is_array($config['options']) ? $config['options'] : [];

        if (isset($config['cache']) && filter_var($config['cache'], FILTER_VALIDATE_BOOLEAN)) {
            if (!class_exists('League\Flysystem\Cached\CachedAdapter')) {
                throw new RequirementsException(
                    sprintf("Install '%s' to use cached adapters", 'league/flysystem-cached-adapter')
                );
            }

            $cacheAdapter = $serviceLocator
                ->get('BsbFlysystemCacheManager')
                ->get($config['cache']);

            $adapter = new CachedAdapter($adapter, $cacheAdapter);
        }

        if (isset($config['eventable']) && filter_var($config['eventable'], FILTER_VALIDATE_BOOLEAN)) {
            if (!class_exists('League\Flysystem\EventableFilesystem\EventableFilesystem')) {
                throw new RequirementsException(
                    sprintf("Install '%s' to use EventableFilesystem", 'league/flysystem-eventable-filesystem')
                );
            }

            $filesystem = new EventableFilesystem($adapter, $options);
        } else {
            $filesystem = new Filesystem($adapter, $options);
        }

        if (isset($config['plugins']) && is_array($config['plugins'])) {
            foreach($config['plugins'] as $plugin) {
                $plugin = new $plugin();
                $filesystem->addPlugin($plugin);
            }
        }

        return $filesystem;
    }
}

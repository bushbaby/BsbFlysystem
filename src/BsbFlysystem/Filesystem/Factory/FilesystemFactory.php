<?php

namespace BsbFlysystem\Filesystem\Factory;

use BsbFlysystem\Exception\RequirementsException;
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

        $cache   = null;
        $options = isset($config['options']) && is_array($config['options']) ? $config['options'] : [];

        if (isset($config['eventable']) && filter_var($config['eventable'], FILTER_VALIDATE_BOOLEAN)) {
            if (!class_exists('League\Flysystem\EventableFilesystem\EventableFilesystem')) {
                throw new RequirementsException(
                    sprintf("Install '%s' to use EventableFilesystem", 'league/flysystem-eventable-filesystem')
                );
            }

            $filesystem = new EventableFilesystem($adapter, $cache, $options);
        } else {
            $filesystem = new Filesystem($adapter, $cache, $options);
        }

        return $filesystem;
    }
}

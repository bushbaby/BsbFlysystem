<?php

namespace BsbFlysystem\Adapter\Factory;

use League\Flysystem\ZipArchive\ZipArchiveAdapter as Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ZipAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->mergeMvcConfig($serviceLocator, func_get_arg(2));

        $this->validateConfig();

        $adapter = new Adapter($this->options['archive'], null, $this->options['prefix']);

        return $adapter;
    }

    /**
     * @inheritdoc
     */
    protected function validateConfig()
    {
        if (!isset($this->options['archive'])) {
            throw new \UnexpectedValueException("Missing 'archive' as option");
        }

        if (!isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

<?php

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\ZipArchive\ZipArchiveAdapter as Adapter;
use Zend\ServiceManager\ServiceLocatorInterface;

class ZipArchiveAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ServiceLocatorInterface $serviceLocator): AdapterInterface
    {
        if (! class_exists(\League\Flysystem\ZipArchive\ZipArchiveAdapter::class)) {
            throw new RequirementsException(
                ['league/ziparchive'],
                'ZipArchive'
            );
        }

        $adapter = new Adapter($this->options['archive'], null, $this->options['prefix']);

        return $adapter;
    }

    protected function validateConfig()
    {
        if (! isset($this->options['archive'])) {
            throw new UnexpectedValueException("Missing 'archive' as option");
        }

        if (! isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

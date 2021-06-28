<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2021 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystem\Filesystem\Factory;

use BsbFlysystem\Cache\ZendStorageCache;
use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use Laminas\Cache\Storage\StorageInterface;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\CacheInterface;
use League\Flysystem\EventableFilesystem\EventableFilesystem;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\FilesystemOperator;
use Psr\Container\ContainerInterface;

class FilesystemFactory
{
    /**
     * @var array
     */
    protected $options;

    public function __construct(array $options = [])
    {
        $this->setCreationOptions($options);
    }

    public function setCreationOptions(array $options): void
    {
        $this->options = $options;

        if (! isset($this->options['adapter_options'])) {
            $this->options['adapter_options'] = [];
        }
    }

    public function createService(ContainerInterface $container): FilesystemOperator
    {
        $requestedName = \func_get_arg(2);

        return $this($container, $requestedName);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FilesystemOperator
    {
        $config = $container->get('config');
        $fsConfig = $config['bsb_flysystem']['filesystems'][$requestedName];
        if (! isset($fsConfig['adapter'])) {
            throw new UnexpectedValueException(\sprintf("Missing 'adapter' key for the filesystem '%s' configuration", $requestedName));
        }

        if (null !== $options) {
            $this->setCreationOptions($options);
        }

        $adapter = $container
            ->get('BsbFlysystemAdapterManager')
            ->get($fsConfig['adapter'], $this->options['adapter_options']);

        $options = $fsConfig['options'] ?? [];


        return new Filesystem($adapter, $options);
    }
}

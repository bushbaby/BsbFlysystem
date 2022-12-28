<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystem\Filesystem\Factory;

use BsbFlysystem\Service\AdapterManager;
use League\Flysystem\Filesystem;
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

        if (\array_key_exists('adapter_options', $this->options)) {
            \assert(
                \is_array($this->options['adapter_options']),
                "Option 'adapter_options' must be an array"
            );
        }
    }

    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null): Filesystem
    {
        $filesystems = ($container->has('config') ? $container->get('config') : [])['bsb_flysystem']['filesystems'] ?? [];

        \assert(
            \array_key_exists($requestedName, $filesystems) && \is_array($filesystems[$requestedName]),
            sprintf("Missing or incorrect configuration for '%s'", $requestedName)
        );

        $configForRequestedFS = $filesystems[$requestedName];

        \assert(
            \array_key_exists('adapter', $configForRequestedFS) && \is_string($configForRequestedFS['adapter']),
            sprintf("Missing or incorrect configuration for the 'config.bsb_flysystem.filesystems.%s.adapter'", $requestedName)
        );

        if (null !== $options) {
            $this->setCreationOptions($options);
        }

        $adapter = $container
            ->get(AdapterManager::class)
            ->get($configForRequestedFS['adapter'], $this->options['adapter_options'] ?? null);

        $options = $configForRequestedFS['options'] ?? [];

        return new Filesystem($adapter, $options);
    }
}

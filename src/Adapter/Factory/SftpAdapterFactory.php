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

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use Psr\Container\ContainerInterface;

class SftpAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): FilesystemAdapter
    {
        if (! class_exists(SftpAdapter::class)) {
            throw new RequirementsException(['league/flysystem-sftp'], 'Sftp');
        }

        if (\array_key_exists('connectivityChecker', $this->options['connectionProvider'])) {
            $this->options['connectionProvider']['connectivityChecker'] = $container->get($this->options['connectionProvider']['connectivityChecker']);
        }

        $this->options['connectionProvider'] = new SftpConnectionProvider(...$this->options['connectionProvider']);

        if (\array_key_exists('visibilityConverter', $this->options)) {
            $this->options['visibilityConverter'] = $container->get($this->options['visibilityConverter']);
        }

        if (\array_key_exists('mimeTypeDetector', $this->options)) {
            $this->options['mimeTypeDetector'] = $container->get($this->options['mimeTypeDetector']);
        }

        return new SftpAdapter(...$this->options);
    }

    protected function validateConfig(): void
    {
        \assert(
            \array_key_exists('connectionProvider', $this->options),
            "Required option 'connectionProvider' is missing"
        );

        \assert(
            \is_array($this->options['connectionProvider']),
            "Option 'connectionProvider' must be an array"
        );

        \assert(
            \array_key_exists('host', $this->options['connectionProvider']),
            "Required option 'connectionProvider.host' is missing"
        );

        \assert(
            \is_string($this->options['connectionProvider']['host']) && ! empty($this->options['connectionProvider']['host']),
            "Option 'connectionProvider.host' must be a non empty string"
        );

        \assert(
            \array_key_exists('username', $this->options['connectionProvider']),
            "Required option 'connectionProvider.username' is missing"
        );

        \assert(
            \is_string($this->options['connectionProvider']['username']) && ! empty($this->options['connectionProvider']['username']),
            "Option 'connectionProvider.username' must be a non empty string"
        );

        \assert(
            \array_key_exists('root', $this->options),
            "Required option 'root' is missing"
        );

        \assert(
            \is_string($this->options['root']) && ! empty($this->options['root']),
            "Option 'root' must be a non empty string"
        );

        if (\array_key_exists('connectivityChecker', $this->options['connectionProvider'])) {
            \assert(
                \is_string($this->options['connectionProvider']['connectivityChecker']) && ! empty($this->options['connectionProvider']['connectivityChecker']),
                "Option 'connectionProvider.connectivityChecker' must be a non empty string"
            );
        }

        if (\array_key_exists('mimeTypeDetector', $this->options)) {
            \assert(
                \is_string($this->options['mimeTypeDetector']) && ! empty($this->options['mimeTypeDetector']),
                "Option 'mimeTypeDetector' must be a non empty string"
            );
        }

        if (\array_key_exists('visibilityConverter', $this->options)) {
            \assert(
                \is_string($this->options['visibilityConverter']) && ! empty($this->options['visibilityConverter']),
                "Option 'visibilityConverter' must be a non empty string"
            );
        }
    }
}

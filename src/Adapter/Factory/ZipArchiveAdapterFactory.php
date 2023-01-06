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
use League\Flysystem\ZipArchive\FilesystemZipArchiveProvider;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Psr\Container\ContainerInterface;

class ZipArchiveAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): FilesystemAdapter
    {
        if (! class_exists(ZipArchiveAdapter::class)) {
            throw new RequirementsException(['league/flysystem-ziparchive'], 'ZipArchive');
        }

        $this->options['zipArchiveProvider'] = new FilesystemZipArchiveProvider(...$this->options['zipArchiveProvider']);

        if (\array_key_exists('mimeTypeDetector', $this->options)) {
            $this->options['mimeTypeDetector'] = $container->get($this->options['mimeTypeDetector']);
        }

        if (\array_key_exists('visibility', $this->options)) {
            $this->options['visibility'] = $container->get($this->options['visibility']);
        }

        return new ZipArchiveAdapter(...$this->options);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function validateConfig(): void
    {
        \assert(
            \array_key_exists('zipArchiveProvider', $this->options),
            "Required option 'zipArchiveProvider' is missing"
        );

        \assert(
            \is_array($this->options['zipArchiveProvider']),
            "Option 'zipArchiveProvider' must be an array"
        );

        \assert(
            \array_key_exists('filename', $this->options['zipArchiveProvider']),
            "Required option 'zipArchiveProvider.filename' is missing"
        );

        \assert(
            \is_string($this->options['zipArchiveProvider']['filename']),
            "Option 'zipArchiveProvider.filename' must be a string"
        );

        if (\array_key_exists('localDirectoryPermissions', $this->options['zipArchiveProvider'])) {
            \assert(
                \is_int($this->options['zipArchiveProvider']['localDirectoryPermissions']),
                "Option 'zipArchiveProvider.localDirectoryPermissions' must be an integer"
            );
        }

        \assert(
            \array_key_exists('root', $this->options),
            "Required option 'root' is missing"
        );

        \assert(
            \is_string($this->options['root']),
            "Option 'root' must be a string"
        );

        if (\array_key_exists('mimeTypeDetector', $this->options)) {
            \assert(
                \is_string($this->options['mimeTypeDetector']) && ! empty($this->options['mimeTypeDetector']),
                "Option 'mimeTypeDetector' must be a non empty string"
            );
        }

        if (\array_key_exists('visibility', $this->options)) {
            \assert(
                \is_string($this->options['visibility']) && ! empty($this->options['visibility']),
                "Option 'visibility' must be a non empty string"
            );
        }
    }
}

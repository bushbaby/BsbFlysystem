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

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Psr\Container\ContainerInterface;

class LocalAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): FilesystemAdapter
    {
        if (\array_key_exists('mimeTypeDetector', $this->options)) {
            $this->options['mimeTypeDetector'] = $container->get($this->options['mimeTypeDetector']);
        }

        if (\array_key_exists('visibility', $this->options)) {
            $this->options['visibility'] = $container->get($this->options['visibility']);
        }

        return new LocalFilesystemAdapter(...$this->options);
    }

    protected function validateConfig(): void
    {
        \assert(
            \array_key_exists('location', $this->options),
            "Required option 'location' is missing"
        );

        \assert(
            \is_string($this->options['location']),
            "Option 'location' must be a string"
        );

        if (\array_key_exists('visibility', $this->options)) {
            \assert(
                \is_string($this->options['visibility']) && ! empty($this->options['visibility']),
                "Option 'visibility' must be a non empty string"
            );
        }

        if (\array_key_exists('writeFlags', $this->options)) {
            \assert(
                \is_int($this->options['writeFlags']),
                "Option 'visibility' must be an integer"
            );
        }

        if (\array_key_exists('linkHandling', $this->options)) {
            \assert(
                \is_int($this->options['LinkHandling']),
                "Option 'LinkHandling' must be an integer"
            );
        }

        if (\array_key_exists('mimeTypeDetector', $this->options)) {
            \assert(
                \is_string($this->options['mimeTypeDetector']) && ! empty($this->options['mimeTypeDetector']),
                "Option 'mimeTypeDetector' must be a non empty string"
            );
        }

        if (\array_key_exists('lazyRootCreation', $this->options)) {
            \assert(
                \is_bool($this->options['lazyRootCreation']),
                "Option 'lazyRootCreation' must be a boolean"
            );
        }
    }
}

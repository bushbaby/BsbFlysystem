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
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use League\Flysystem\Visibility;
use Psr\Container\ContainerInterface;

class GoogleCloudStorageAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): FilesystemAdapter
    {
        if (! class_exists(GoogleCloudStorageAdapter::class)) {
            throw new RequirementsException(['league/flysystem-google-cloud-storage'], 'GoogleCloudDrive');
        }

        $this->options['bucket'] = $container->get($this->options['bucket']);

        if (isset($this->options['visibilityHandler'])) {
            $this->options['visibilityHandler'] = $container->get($this->options['visibilityHandler']);
        }

        if (isset($this->options['mimeTypeDetector'])) {
            $this->options['mimeTypeDetector'] = $container->get($this->options['mimeTypeDetector']);
        }

        return new GoogleCloudStorageAdapter(...$this->options);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function validateConfig(): void
    {
        \assert(
            \array_key_exists('bucket', $this->options),
            "Required option 'bucket' is missing"
        );

        \assert(
            \is_string($this->options['bucket']) && ! empty($this->options['bucket']),
            "Option 'bucket' must be a non empty string"
        );

        if (\array_key_exists('mimeTypeDetector', $this->options)) {
            \assert(
                \is_string($this->options['mimeTypeDetector']) && ! empty($this->options['mimeTypeDetector']),
                "Option 'mimeTypeDetector' must be a non empty string"
            );
        }

        if (\array_key_exists('defaultVisibility', $this->options)) {
            \assert(
                \is_string($this->options['defaultVisibility']) && ! empty($this->options['defaultVisibility']),
                "Option 'defaultVisibility' must be a non empty string"
            );
            \assert(
                \in_array($this->options['defaultVisibility'], [Visibility::PUBLIC, Visibility::PRIVATE], true),
                sprintf("Option 'defaultVisibility' must either be '%s' or '%s'", Visibility::PUBLIC, Visibility::PRIVATE)
            );
        }

        if (\array_key_exists('visibilityHandler', $this->options)) {
            \assert(
                \is_string($this->options['visibilityHandler']) && ! empty($this->options['visibilityHandler']),
                "Option 'visibilityHandler' must be a non empty string"
            );
        }
    }
}

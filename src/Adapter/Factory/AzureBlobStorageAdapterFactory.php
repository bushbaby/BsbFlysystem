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
use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use League\Flysystem\FilesystemAdapter;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;
use Psr\Container\ContainerInterface;

class AzureBlobStorageAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): FilesystemAdapter
    {
        if (! class_exists(AzureBlobStorageAdapter::class)) {
            throw new RequirementsException(['league/flysystem-azure'], 'Azure');
        }

        if (\is_string($this->options['client'])) {
            $this->options['client'] = $container->get($this->options['client']);
        } else {
            $this->options['client'] = BlobRestProxy::createBlobService(...$this->options['client']);
        }

        if (\array_key_exists('mimeTypeDetector', $this->options)) {
            $this->options['mimeTypeDetector'] = $container->get($this->options['mimeTypeDetector']);
        }

        if (isset($this->options['serviceSettings'])) {
            $this->options['serviceSettings'] = new StorageServiceSettings(...$this->options['serviceSettings']);
        }

        return new AzureBlobStorageAdapter(...$this->options);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function validateConfig(): void
    {
        \assert(
            \array_key_exists('client', $this->options),
            "Required option 'client' is missing"
        );

        \assert(
            (\is_string($this->options['client']) && ! empty($this->options['client']))
            || \is_array($this->options['client']),
            "Option 'client' must either be a non empty string or an array"
        );

        if (\is_array($this->options['client'])) {
            \assert(
                \array_key_exists('connectionString', $this->options['client']),
                "Required option 'client.connectionString' is missing"
            );

            \assert(
                \is_string($this->options['client']['connectionString']) && (! empty($this->options['client']['connectionString']))
                || \is_array($this->options['client']['connectionString']),
                "Option 'client.connectionString' must either be a non empty string or an array"
            );

            if (\array_key_exists('options', $this->options['client'])) {
                \assert(
                    \is_array($this->options['client']['options']),
                    "Option 'client.options' must be an array"
                );
            }
        }

        \assert(
            \array_key_exists('container', $this->options),
            "Required option 'container' is missing"
        );

        \assert(
            \is_string($this->options['container']) && ! empty($this->options['container']),
            "Option 'container' must be a non empty string"
        );

        if (\array_key_exists('mimeTypeDetector', $this->options)) {
            \assert(
                \is_string($this->options['mimeTypeDetector']) && ! empty($this->options['mimeTypeDetector']),
                "Option 'mimeTypeDetector' must be a non empty string"
            );
        }

        if (\array_key_exists('prefix', $this->options)) {
            \assert(
                \is_string($this->options['prefix']),
                "Option 'prefix' must be a string"
            );
        }

        if (\array_key_exists('maxResultsForContentsListing', $this->options)) {
            \assert(
                \is_int($this->options['maxResultsForContentsListing']),
                "Option 'maxResultsForContentsListing' must be an integer"
            );
        }

        if (\array_key_exists('visibilityHandling', $this->options)) {
            \assert(
                \is_string($this->options['visibilityHandling']),
                "Option 'visibilityHandling' must be a string"
            );

            \assert(
                \in_array($this->options['visibilityHandling'], [AzureBlobStorageAdapter::ON_VISIBILITY_THROW_ERROR, AzureBlobStorageAdapter::ON_VISIBILITY_IGNORE], true),
                sprintf("Option 'visibilityHandling' must either be '%s' or '%s'", AzureBlobStorageAdapter::ON_VISIBILITY_THROW_ERROR, AzureBlobStorageAdapter::ON_VISIBILITY_IGNORE)
            );
        }

        if (\array_key_exists('serviceSettings', $this->options)) {
            \assert(
                \is_array($this->options['serviceSettings']),
                "Option 'serviceSettings' must be an array"
            );

            \assert(
                \array_key_exists('name', $this->options['serviceSettings']),
                "Required option 'serviceSettings.name' is missing"
            );

            \assert(
                \is_string($this->options['serviceSettings']['name']) && ! empty($this->options['serviceSettings']['name']),
                "Option 'serviceSettings.name' must be a non empty string"
            );

            \assert(
                \array_key_exists('key', $this->options['serviceSettings']),
                "Required option 'serviceSettings.key' is missing"
            );

            \assert(
                \is_string($this->options['serviceSettings']['key']) && ! empty($this->options['serviceSettings']['key']),
                "Option 'serviceSettings.key' must be a non empty string"
            );

            \assert(
                \array_key_exists('blobEndpointUri', $this->options['serviceSettings']),
                "Required option 'serviceSettings.blobEndpointUri' is missing"
            );

            \assert(
                \is_string($this->options['serviceSettings']['blobEndpointUri']) && ! empty($this->options['serviceSettings']['blobEndpointUri']),
                "Option 'serviceSettings.blobEndpointUri' must be a non empty string"
            );

            \assert(
                \array_key_exists('queueEndpointUri', $this->options['serviceSettings']),
                "Required option 'serviceSettings.queueEndpointUri' is missing"
            );

            \assert(
                \is_string($this->options['serviceSettings']['queueEndpointUri']) && ! empty($this->options['serviceSettings']['queueEndpointUri']),
                "Option 'serviceSettings.queueEndpointUri' must be a non empty string"
            );
            \assert(
                \array_key_exists('queueEndpointUri', $this->options['serviceSettings']),
                "Required option 'serviceSettings.queueEndpointUri' is missing"
            );

            \assert(
                \is_string($this->options['serviceSettings']['queueEndpointUri']) && ! empty($this->options['serviceSettings']['queueEndpointUri']),
                "Option 'serviceSettings.queueEndpointUri' must be a non empty string"
            );
            \assert(
                \array_key_exists('tableEndpointUri', $this->options['serviceSettings']),
                "Required option 'serviceSettings.tableEndpointUri' is missing"
            );

            \assert(
                \is_string($this->options['serviceSettings']['tableEndpointUri']) && ! empty($this->options['serviceSettings']['tableEndpointUri']),
                "Option 'serviceSettings.tableEndpointUri' must be a non empty string"
            );

            \assert(
                \array_key_exists('fileEndpointUri', $this->options['serviceSettings']),
                "Required option 'serviceSettings.fileEndpointUri' is missing"
            );

            \assert(
                \is_string($this->options['serviceSettings']['fileEndpointUri']) && ! empty($this->options['serviceSettings']['fileEndpointUri']),
                "Option 'serviceSettings.fileEndpointUri' must be a non empty string"
            );
        }
    }
}

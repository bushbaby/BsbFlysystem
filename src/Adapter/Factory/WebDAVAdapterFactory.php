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

use Assert\Assertion;
use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\WebDAV\WebDAVAdapter;
use Psr\Container\ContainerInterface;
use Sabre\DAV\Client;

class WebDAVAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): FilesystemAdapter
    {
        if (! class_exists(WebDAVAdapter::class)) {
            throw new RequirementsException(['league/flysystem-webdav'], 'WebDAV');
        }

        $this->options['client'] = new Client($this->options['client']);

        return new WebDAVAdapter(...$this->options);
    }

    protected function validateConfig(): void
    {
        \assert(
            \array_key_exists('client', $this->options),
            "Required option 'client' is missing"
        );

        \assert(
            \is_array($this->options['client']),
            "Option 'client' must be an array"
        );

        \assert(
            \array_key_exists('baseUri', $this->options['client']),
            "Required option 'client.baseUri' is missing"
        );

        \assert(
            \is_string($this->options['client']['baseUri']) && ! empty($this->options['client']['baseUri']),
            "Option 'client.baseUri' must be a non empty string"
        );

        if (isset($this->options['client']['userName'])) {
            \assert(
                \is_string($this->options['client']['userName']) && ! empty($this->options['client']['userName']),
                "Option 'client.userName' must be a non empty string"
            );
        }

        if (isset($this->options['client']['password'])) {
            \assert(
                \is_string($this->options['client']['password']) && ! empty($this->options['client']['password']),
                "Option 'client.password' must be a non empty string"
            );
        }

        if (isset($this->options['client']['proxy'])) {
            \assert(
                \is_string($this->options['client']['proxy']),
                "Option 'client.proxy' must be a string"
            );
        }
        if (isset($this->options['client']['encoding'])) {
            \assert(
                \is_string($this->options['client']['encoding']),
                "Option 'client.encoding' must be a string"
            );
        }

        if (isset($this->options['client']['authType'])) {
            \assert(
                \is_int($this->options['client']['authType']),
                "Option 'client.authType' must be an integer"
            );
        }

        if (isset($this->options['prefix'])) {
            \assert(
                \is_string($this->options['prefix']) && ! empty($this->options['prefix']),
                "Option 'prefix' must be a non empty string"
            );
        }

        if (isset($this->options['visibilityHandling'])) {
            \assert(
                \is_string($this->options['visibilityHandling']),
                "Option 'visibilityHandling' must be a string"
            );

            \assert(
                \in_array($this->options['visibilityHandling'], [WebDAVAdapter::ON_VISIBILITY_THROW_ERROR, WebDAVAdapter::ON_VISIBILITY_IGNORE], true),
                sprintf("Option 'visibilityHandling' must either be '%s' or '%s'", WebDAVAdapter::ON_VISIBILITY_THROW_ERROR, WebDAVAdapter::ON_VISIBILITY_IGNORE)
            );
        }

        if (isset($this->options['manualCopy'])) {
            Assertion::boolean($this->options['manualCopy'], sprintf("Option '%s' must be a boolean", 'manualCopy'));
        }

        if (isset($this->options['manualMove'])) {
            Assertion::boolean($this->options['manualMove'], sprintf("Option '%s' must be a boolean", 'manualMove'));
        }
    }
}

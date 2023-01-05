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
use Psr\Container\ContainerInterface;
use Spatie\Dropbox\Client;
use Spatie\Dropbox\TokenProvider;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): FilesystemAdapter
    {
        if (! class_exists(DropboxAdapter::class)) {
            throw new RequirementsException(['spatie/flysystem-dropbox'], 'Dropbox');
        }

        if (\is_string($this->options['client'])) {
            $this->options['client'] = $container->get($this->options['client']);
        } else {
            $this->options['client'] = new Client(...$this->options['client']);
        }

        if (\array_key_exists('mimeTypeDetector', $this->options)) {
            $this->options['mimeTypeDetector'] = $container->get($this->options['mimeTypeDetector']);
        }

        return new DropboxAdapter(...$this->options);
    }

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
                \array_key_exists('accessTokenOrAppCredentials', $this->options['client']),
                "Required option 'client.accessTokenOrAppCredentials' is missing"
            );

            \assert(
                (\is_string($this->options['client']['accessTokenOrAppCredentials']) && ! empty($this->options['client']['accessTokenOrAppCredentials']))
                || \is_array($this->options['client']['accessTokenOrAppCredentials'])
                || $this->options['client']['accessTokenOrAppCredentials'] instanceof TokenProvider,
                "Option 'client.accessTokenOrAppCredentials' must either be a non empty string, an array or an instance of 'Spatie\Dropbox\TokenProvider'"
            );

            if (\is_array($this->options['client']['accessTokenOrAppCredentials'])) {
                \assert(
                    2 === \count($this->options['client']['accessTokenOrAppCredentials']),
                    "Option 'client.accessTokenOrAppCredentials' must be an array with 2 elements"
                );
            }
        }

        if (\array_key_exists('mimeTypeDetector', $this->options)) {
            \assert(
                \is_string($this->options['mimeTypeDetector']) && ! empty($this->options['mimeTypeDetector']),
                "Option 'mimeTypeDetector' must be a non empty string"
            );
        }
    }
}

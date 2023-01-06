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
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\Visibility;
use Psr\Container\ContainerInterface;

class InMemoryAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): FilesystemAdapter
    {
        if (! class_exists(InMemoryFilesystemAdapter::class)) {
            throw new RequirementsException(['league/flysystem-memory'], 'InMemory');
        }

        if (\array_key_exists('mimeTypeDetector', $this->options)) {
            $this->options['mimeTypeDetector'] = $container->get($this->options['mimeTypeDetector']);
        }

        return new InMemoryFilesystemAdapter(...$this->options);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function validateConfig(): void
    {
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
    }
}

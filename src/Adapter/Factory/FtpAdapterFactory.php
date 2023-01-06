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
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use Psr\Container\ContainerInterface;

class FtpAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): FilesystemAdapter
    {
        if (! class_exists(FtpAdapter::class)) {
            throw new RequirementsException(['league/flysystem-ftp'], 'Ftp');
        }

        $this->options['connectionOptions'] = new FtpConnectionOptions(...$this->options['connectionOptions']);

        if (\array_key_exists('connectionProvider', $this->options)) {
            $this->options['connectionProvider'] = $container->get($this->options['connectionProvider']);
        }

        if (\array_key_exists('connectivityChecker', $this->options)) {
            $this->options['connectivityChecker'] = $container->get($this->options['connectivityChecker']);
        }

        if (\array_key_exists('visibilityConverter', $this->options)) {
            $this->options['visibilityConverter'] = $container->get($this->options['visibilityConverter']);
        }

        if (\array_key_exists('mimeTypeDetector', $this->options)) {
            $this->options['mimeTypeDetector'] = $container->get($this->options['mimeTypeDetector']);
        }

        return new FtpAdapter(...$this->options);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function validateConfig(): void
    {
        \assert(
            \array_key_exists('connectionOptions', $this->options),
            "Required option 'connectionOptions' is missing"
        );

        \assert(
            \is_array($this->options['connectionOptions']),
            "Option 'connectionOptions' must be an array"
        );

        \assert(
            \array_key_exists('host', $this->options['connectionOptions']),
            "Required option 'connectionOptions.host' is missing"
        );

        \assert(
            \is_string($this->options['connectionOptions']['host']) && ! empty($this->options['connectionOptions']['host']),
            "Option 'connectionOptions.host' must be a non empty string"
        );

        \assert(
            \array_key_exists('root', $this->options['connectionOptions']),
            "Required option 'connectionOptions.root' is missing"
        );

        \assert(
            \is_string($this->options['connectionOptions']['root']) && ! empty($this->options['connectionOptions']['root']),
            "Option 'connectionOptions.root' must be a non empty string"
        );

        \assert(
            \array_key_exists('username', $this->options['connectionOptions']),
            "Required option 'connectionOptions.username' is missing"
        );

        \assert(
            \is_string($this->options['connectionOptions']['username']) && ! empty($this->options['connectionOptions']['username']),
            "Option 'connectionOptions.username' must be a non empty string"
        );

        \assert(
            \array_key_exists('password', $this->options['connectionOptions']),
            "Required option 'connectionOptions.password' is missing"
        );

        \assert(
            \is_string($this->options['connectionOptions']['password']),
            "Option 'connectionOptions.password' must be a string"
        );

        if (\array_key_exists('connectionProvider', $this->options)) {
            \assert(
                \is_string($this->options['connectionProvider']) && ! empty($this->options['connectionProvider']),
                "Option 'connectionProvider' must be a non empty string"
            );
        }

        if (\array_key_exists('connectivityChecker', $this->options)) {
            \assert(
                \is_string($this->options['connectivityChecker']) && ! empty($this->options['connectivityChecker']),
                "Option 'connectivityChecker' must be a non empty string"
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

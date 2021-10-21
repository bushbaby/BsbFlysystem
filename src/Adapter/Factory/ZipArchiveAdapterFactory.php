<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2021 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use League\Flysystem\ZipArchive\ZipArchiveAdapter as Adapter;
use League\Flysystem\ZipArchive\ZipArchiveProvider;
use League\MimeTypeDetection\MimeTypeDetector;
use Psr\Container\ContainerInterface;

class ZipArchiveAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): FilesystemAdapter
    {
        if (! class_exists(Adapter::class)) {
            throw new RequirementsException(['league/ziparchive'], 'ZipArchive');
        }

        $zipArchiveProvider = $this->options['zip_archive_provider'] ?? null;

        if (is_string($zipArchiveProvider)) {
            $zipArchiveProvider = $container->get($zipArchiveProvider);
        }
        if (! $zipArchiveProvider instanceof ZipArchiveProvider) {
            throw new UnexpectedValueException('Missing required ZipArchiveProvider');
        }

        $root = $this->options['root'] ?? '';

        if (! is_string($root)) {
            throw new UnexpectedValueException('Root must be a string');
        }

        $mimeTypeDetector = $this->options['mime_type_detector'] ?? null;

        if (is_string($mimeTypeDetector)) {
            $mimeTypeDetector = $container->get($mimeTypeDetector);
        }

        if (! $mimeTypeDetector instanceof MimeTypeDetector && $mimeTypeDetector !== null) {
            throw new UnexpectedValueException('mime_type_detector must be a service, service name or null');
        }

        $visibility = $this->options['visibility'] ?? null;

        if (is_string($visibility)) {
            $visibility = $container->get($visibility);
        }

        if (! $visibility instanceof VisibilityConverter && $visibility !== null) {
            throw new UnexpectedValueException('visibility must be a service, service name or null');
        }

        return new Adapter($zipArchiveProvider, $root, $mimeTypeDetector, $visibility);
    }

    protected function validateConfig(): void
    {
    }
}

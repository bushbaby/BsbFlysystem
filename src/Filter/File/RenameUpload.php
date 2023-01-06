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

namespace BsbFlysystem\Filter\File;

use Laminas\Filter\Exception;
use Laminas\Filter\File\RenameUpload as RenameUploadFilter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

class RenameUpload extends RenameUploadFilter
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @throws \UnexpectedValueException
     */
    public function getFilesystem(): Filesystem
    {
        if (! $this->filesystem) {
            throw new \UnexpectedValueException('Missing required filesystem.');
        }

        return $this->filesystem;
    }

    public function setFilesystem(Filesystem $filesystem): void
    {
        $this->filesystem = $filesystem;
    }

    protected function getFinalTarget($uploadData, $clientFileName): string
    {
        return trim(str_replace('\\', '/', parent::getFinalTarget($uploadData, $clientFileName)), '/');
    }

    protected function checkFileExists($targetFile): void
    {
        if (! $this->getOverwrite() && $this->getFilesystem()->has($targetFile)) {
            throw new Exception\InvalidArgumentException(sprintf("File '%s' could not be uploaded. It already exists.", $targetFile));
        }
    }

    protected function moveUploadedFile($sourceFile, $targetFile): bool
    {
        if (! is_uploaded_file($sourceFile)) {
            throw new Exception\RuntimeException(sprintf("File '%s' could not be uploaded. Filter can move only uploaded files.", $sourceFile));
        }

        $stream = fopen($sourceFile, 'r+');

        try {
            $this->getFilesystem()->writeStream($targetFile, $stream);
        } catch (FilesystemException $e) {
            throw new Exception\RuntimeException(sprintf("File '%s' could not be uploaded. An error occurred while processing the file.", $sourceFile));
        } finally {
            fclose($stream);
        }

        return true;
    }
}

<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2019 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystem\Filter\File;

use League\Flysystem\FilesystemInterface;
use UnexpectedValueException;
use Zend\Filter\Exception;
use Zend\Filter\File\RenameUpload as RenameUploadFilter;

class RenameUpload extends RenameUploadFilter
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @throws UnexpectedValueException
     */
    public function getFilesystem(): FilesystemInterface
    {
        if (! $this->filesystem) {
            throw new UnexpectedValueException('Missing required filesystem.');
        }

        return $this->filesystem;
    }

    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    protected function getFinalTarget($uploadData, $clientFileName): string
    {
        return \trim(\str_replace('\\', '/', parent::getFinalTarget($uploadData, $clientFileName)), '/');
    }

    protected function checkFileExists($targetFile)
    {
        if (! $this->getOverwrite() && $this->getFilesystem()->has($targetFile)) {
            throw new Exception\InvalidArgumentException(
                \sprintf("File '%s' could not be uploaded. It already exists.", $targetFile)
            );
        }
    }

    protected function moveUploadedFile($sourceFile, $targetFile)
    {
        if (! is_uploaded_file($sourceFile)) {
            throw new Exception\RuntimeException(
                \sprintf("File '%s' could not be uploaded. Filter can move only uploaded files.", $sourceFile),
                0
            );
        }
        $stream = \fopen($sourceFile, 'r+');
        $result = $this->getFilesystem()->putStream($targetFile, $stream);
        \fclose($stream);

        if (! $result) {
            throw new Exception\RuntimeException(
                \sprintf("File '%s' could not be uploaded. An error occurred while processing the file.", $sourceFile),
                0
            );
        }

        return $result;
    }
}

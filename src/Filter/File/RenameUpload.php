<?php

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

    protected function getFinalTarget($uploadData): string
    {
        return trim(str_replace('\\', '/', parent::getFinalTarget($uploadData)), '/');
    }

    protected function checkFileExists($targetFile)
    {
        if (! $this->getOverwrite() && $this->getFilesystem()->has($targetFile)) {
            throw new Exception\InvalidArgumentException(
                sprintf("File '%s' could not be uploaded. It already exists.", $targetFile)
            );
        }
    }

    protected function moveUploadedFile($sourceFile, $targetFile)
    {
        if (! is_uploaded_file($sourceFile)) {
            throw new Exception\RuntimeException(
                sprintf("File '%s' could not be uploaded. Filter can move only uploaded files.", $sourceFile),
                0
            );
        }
        $stream = fopen($sourceFile, 'r+');
        $result = $this->getFilesystem()->putStream($targetFile, $stream);
        fclose($stream);

        if (! $result) {
            throw new Exception\RuntimeException(
                sprintf("File '%s' could not be uploaded. An error occurred while processing the file.", $sourceFile),
                0
            );
        }

        return $result;
    }
}

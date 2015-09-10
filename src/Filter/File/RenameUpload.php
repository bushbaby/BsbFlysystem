<?php

namespace BsbFlysystem\Filter\File;

use League\Flysystem\FilesystemInterface;
use UnexpectedValueException;
use Zend\Filter\File\RenameUpload as RenameUploadFilter;
use Zend\Filter\Exception;
use Zend\Stdlib\ErrorHandler;

class RenameUpload extends RenameUploadFilter
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options)
    {
        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '"%s" expects an array; received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }
        parent::__construct($options);
    }

    /**
     * @throws UnexpectedValueException
     * @return FilesystemInterface
     */
    public function getFilesystem()
    {
        if (!$this->filesystem) {
            throw new UnexpectedValueException('Missing required filesystem.');
        }

        return $this->filesystem;
    }

    /**
     * @param FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritdoc
     */
    protected function getFinalTarget($uploadData)
    {
        $path = trim(str_replace('\\', '/', parent::getFinalTarget($uploadData)), '/');
        if (strpos($path, './') === 0) {
            $path = substr($path, 2);
        }

        return $path;
    }

    /**
     * @inheritdoc
     */
    protected function checkFileExists($targetFile)
    {
        if (!$this->getOverwrite() && $this->getFilesystem()->has($targetFile)) {
            throw new Exception\InvalidArgumentException(
                sprintf("File '%s' could not be uploaded. It already exists.", $targetFile)
            );
        }
    }

    /**
     * @inheritdoc
     */
    protected function moveUploadedFile($sourceFile, $targetFile)
    {
        ErrorHandler::start();
        $stream = fopen($sourceFile, 'r+');
        $result = $this->getFilesystem()->putStream($targetFile, $stream);
        fclose($stream);
        $filesystemException = ErrorHandler::stop();

        if (!$result || null !== $filesystemException) {
            throw new Exception\RuntimeException(
                sprintf("File '%s' could not be uploaded. An error occurred while processing the file.", $sourceFile),
                0,
                $filesystemException
            );
        }

        return $result;
    }
}

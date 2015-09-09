<?php

namespace BsbFlysystem\Filter\File;

use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
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
     * @return FilesystemInterface
     */
    public function getFilesystem()
    {
        if (!$this->filesystem) {
            // Fallback to local and guess required root path.
            $root = $this->guessRootPath();

            // Warning: changing target path later will not update root path.
            $this->filesystem = new Filesystem(new LocalAdapter($root));
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

    /**
     * Will try to guess root path for local adapter
     *
     * @return string
     */
    private function guessRootPath()
    {
        $target = $this->getTarget();
        $path = realpath($target);
        if (strpos($target, './') === 0) {
            $target = substr($target, 2);
        }

        $root = substr_replace($path, '', strrpos($path, $target), strlen($path));
        // set working dir as root if impossible to guess root path
        return $root ?: getcwd();
    }
}

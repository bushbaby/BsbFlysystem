<?php

declare(strict_types=1);

namespace BsbFlysystemTest\Filter\File;

use BsbFlysystem\Filter\File\RenameUpload;
use BsbFlysystemTest\Framework\TestCase;
use League\Flysystem\FilesystemInterface;
use Prophecy\Argument;

require_once __DIR__ . '/../../Assets/Functions.php';

class RenameUploadTest extends TestCase
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    public function setup()
    {
        $this->filesystem = $this->prophesize(FilesystemInterface::class);
    }

    public function testCanUploadFile()
    {
        $path = 'path/to/file.txt';
        $this->filesystem->putStream($path, Argument::any())
            ->willReturn(true)
            ->shouldBeCalled();
        $this->filesystem->has($path)
            ->willReturn(false);

        $filter = new RenameUpload([
            'target'     => $path,
            'filesystem' => $this->filesystem->reveal(),
        ]);

        $key = $filter->filter(__DIR__ . '/../../Assets/test.txt');
        $this->assertEquals($path, $key);
    }

    public function testCanUploadFileWhenUploading()
    {
        $path = 'path/to/file.txt';
        $this->filesystem->putStream($path, Argument::any())
            ->willReturn(true)
            ->shouldBeCalled();
        $this->filesystem->has($path)
            ->willReturn(false);

        $filter = new RenameUpload([
            'target'     => $path,
            'filesystem' => $this->filesystem->reveal(),
        ]);

        $file = [
            'tmp_name' => __DIR__ . '/../../Assets/test.txt',
            'name'     => 'test.txt',
        ];
        $temp = $filter->filter($file);

        $this->assertEquals($path, $temp['tmp_name']);
    }

    public function testWillThrowExceptionWhenFilesystemNotSet()
    {
        $filter = new RenameUpload([
            'target' => 'path/to/file.txt',
        ]);

        $this->expectException(\UnexpectedValueException::class);
        $filter->filter(__DIR__ . '/../../Assets/test.txt');
    }

    public function testWillThrowExceptionWhenFileIsNotPostUploaded()
    {
        $path = 'path/to/file.txt';
        $this->filesystem->has($path)
            ->willReturn(false);

        $filter = new RenameUpload([
            'target'     => $path,
            'filesystem' => $this->filesystem->reveal(),
        ]);

        $this->expectException(\Zend\Filter\Exception\RuntimeException::class);
        $this->expectExceptionMessage("File '".__DIR__ . '/../../Assets/Functions.php'."' could not be uploaded. Filter can move only uploaded files.");
        $filter->filter(__DIR__ . '/../../Assets/Functions.php');
    }

    public function testWillThrowExceptionWhenFileExists()
    {
        $path = 'path/to/file.txt';
        $this->filesystem->has($path)
            ->willReturn(true)
            ->shouldBeCalled();

        $filter = new RenameUpload([
            'target'     => $path,
            'overwrite'  => false,
            'filesystem' => $this->filesystem->reveal(),
        ]);

        $this->expectException(\Zend\Filter\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage("File 'path/to/file.txt' could not be uploaded. It already exists.");
        $filter->filter(__DIR__ . '/../../Assets/test.txt');
    }

    public function testWillThrowExceptionWhenFilesystemFails()
    {
        $path = 'path/to/file.txt';
        $this->filesystem->putStream($path, Argument::any())
            ->willReturn(false)
            ->shouldBeCalled();
        $this->filesystem->has($path)
            ->willReturn(false);

        $filter = new RenameUpload([
            'target'     => $path,
            'filesystem' => $this->filesystem->reveal(),
        ]);

        $this->expectException(\Zend\Filter\Exception\RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            "File '%s' could not be uploaded. An error occurred while processing the file.",
            __DIR__ . '/../../Assets/test.txt'
        ));
        $filter->filter(__DIR__ . '/../../Assets/test.txt');
    }
}

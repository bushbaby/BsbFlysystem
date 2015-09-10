<?php

namespace BsbFlysystemTest\Filter\File;

use BsbFlysystem\Filter\File\RenameUpload;
use BsbFlysystemTest\Framework\TestCase;
use Prophecy\Argument;

class RenameUploadTest extends TestCase
{
    protected $filesystem;

    public function setUp()
    {
        $this->filesystem = $this->prophesize('League\Flysystem\FilesystemInterface');
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
            'target' => $path,
            'filesystem' => $this->filesystem->reveal()
        ]);

        $key = $filter->filter(__DIR__ . '/../../Assets/test.txt');
        $this->assertEquals($path, $key);
    }

    public function testWillThrowExceptionWithInvalidConstructorParams()
    {
        $this->setExpectedException('Zend\Filter\Exception\InvalidArgumentException');
        new RenameUpload('something');
    }

    public function testWillThrowExceptionWhenFilesystemNotSet()
    {
        $filter = new RenameUpload([
            'target' => 'path/to/file.txt',
        ]);

        $this->setExpectedException('UnexpectedValueException');
        $filter->filter(__DIR__ . '/../../Assets/test.txt');
    }

    public function testWillThrowExceptionWhenFileExists()
    {
        $path = 'path/to/file.txt';
        $this->filesystem->has($path)
            ->willReturn(true)
            ->shouldBeCalled();

        $filter = new RenameUpload([
            'target' => $path,
            'overwrite' => false,
            'filesystem' => $this->filesystem->reveal()
        ]);

        $this->setExpectedException('Zend\Filter\Exception\InvalidArgumentException', "File 'path/to/file.txt' could not be uploaded. It already exists.");
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
            'target' => $path,
            'filesystem' => $this->filesystem->reveal()
        ]);

        $this->setExpectedException(
            'Zend\Filter\Exception\RuntimeException',
            sprintf(
                "File '%s' could not be uploaded. An error occurred while processing the file.",
                __DIR__ . '/../../Assets/test.txt'
            )
        );
        $filter->filter(__DIR__ . '/../../Assets/test.txt');
    }
}

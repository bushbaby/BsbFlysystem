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

namespace BsbFlysystemTest\Filter\File;

use BsbFlysystem\Filter\File\RenameUpload;
use Laminas\Filter\Exception\InvalidArgumentException;
use Laminas\Filter\Exception\RuntimeException;
use League\Flysystem\Filesystem;
use League\Flysystem\UnableToWriteFile;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophet;

require_once __DIR__ . '/../../Assets/Functions.php';

class RenameUploadTest extends TestCase
{
    protected Prophet $prophet;

    protected function setUp(): void
    {
        $this->prophet = new Prophet();
    }

    protected function tearDown(): void
    {
        $this->prophet->checkPredictions();
    }

    public function testCanUploadFile(): void
    {
        $path = 'path/to/file.txt';

        $filesystem = $this->prophet->prophesize(Filesystem::class);
        $filesystem->writeStream($path, Argument::any());

        $filesystem->has($path)
            ->willReturn(false);

        $filter = new RenameUpload([
            'target' => $path,
            'filesystem' => $filesystem->reveal(),
        ]);

        $key = $filter->filter(__DIR__ . '/../../Assets/test.txt');
        $this->assertEquals($path, $key);
    }

    public function testCanUploadFileWhenUploading(): void
    {
        $path = 'path/to/file.txt';
        $filesystem = $this->prophet->prophesize(Filesystem::class);

        $filesystem->writeStream($path, Argument::any());
        $filesystem->has($path)->willReturn(false);

        $filter = new RenameUpload([
            'target' => $path,
            'filesystem' => $filesystem->reveal(),
        ]);

        $file = [
            'tmp_name' => __DIR__ . '/../../Assets/test.txt',
            'name' => 'test.txt',
        ];
        $temp = $filter->filter($file);

        $this->assertEquals($path, $temp['tmp_name']);
    }

    public function testWillThrowExceptionWhenFilesystemNotSet(): void
    {
        $filter = new RenameUpload([
            'target' => 'path/to/file.txt',
        ]);

        $this->expectException(\UnexpectedValueException::class);
        $filter->filter(__DIR__ . '/../../Assets/test.txt');
    }

    public function testWillThrowExceptionWhenFileIsNotPostUploaded(): void
    {
        $path = 'path/to/file.txt';
        $filesystem = $this->prophet->prophesize(Filesystem::class);
        $filesystem->has($path)->willReturn(false);

        $filter = new RenameUpload([
            'target' => $path,
            'filesystem' => $filesystem->reveal(),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("File '".__DIR__ . '/../../Assets/Functions.php'."' could not be uploaded. Filter can move only uploaded files.");
        $filter->filter(__DIR__ . '/../../Assets/Functions.php');
    }

    public function testWillThrowExceptionWhenFileExists(): void
    {
        $path = 'path/to/file.txt';
        $filesystem = $this->prophet->prophesize(Filesystem::class);
        $filesystem->has($path)->willReturn(true);

        $filter = new RenameUpload([
            'target' => $path,
            'overwrite' => false,
            'filesystem' => $filesystem->reveal(),
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("File 'path/to/file.txt' could not be uploaded. It already exists.");

        $filter->filter(__DIR__ . '/../../Assets/test.txt');
    }

    public function testWillThrowExceptionWhenFilesystemFails(): void
    {
        $path = 'path/to/file.txt';
        $filesystem = $this->prophet->prophesize(Filesystem::class);
        $filesystem->writeStream($path, Argument::any())->willThrow(UnableToWriteFile::class);
        $filesystem->has($path)->willReturn(false);

        $filter = new RenameUpload([
            'target' => $path,
            'filesystem' => $filesystem->reveal(),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            "File '%s' could not be uploaded. An error occurred while processing the file.",
            __DIR__ . '/../../Assets/test.txt'
        ));

        $filter->filter(__DIR__ . '/../../Assets/test.txt');
    }
}

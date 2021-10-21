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

namespace BsbFlysystemTest\Filter\File;

use BsbFlysystem\Filter\File\RenameUpload;
use Laminas\Filter\Exception\InvalidArgumentException;
use Laminas\Filter\Exception\RuntimeException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\UnableToWriteFile;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use UnexpectedValueException;

require_once __DIR__ . '/../../Assets/Functions.php';

class RenameUploadTest extends TestCase
{
    use ProphecyTrait;

    const PATH = 'path/to/file.txt';

    /**
     * @var FilesystemOperator
     */
    protected $filesystem;

    public function setup(): void
    {
        $this->filesystem = new Filesystem(new InMemoryFilesystemAdapter());
    }

    public function testCanUploadFile(): void
    {
        $key = $this->filter()->filter(__DIR__ . '/../../Assets/test.txt');
        $this->assertEquals(self::PATH, $key);
    }

    public function testCanUploadFileWhenUploading(): void
    {
        $file = [
            'tmp_name' => __DIR__ . '/../../Assets/test.txt',
            'name' => 'test.txt',
        ];
        $temp = $this->filter()->filter($file);

        $this->assertEquals(self::PATH, $temp['tmp_name']);
    }

    public function testWillThrowExceptionWhenFilesystemNotSet(): void
    {
        $filter = new RenameUpload([
            'target' => 'path/to/file.txt',
        ]);

        $this->expectException(UnexpectedValueException::class);
        $filter->filter(__DIR__ . '/../../Assets/test.txt');
    }

    public function testWillThrowExceptionWhenFileIsNotPostUploaded(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("File '".__DIR__ . '/../../Assets/Functions.php'."' could not be uploaded. Filter can move only uploaded files.");

        $this->filter()->filter(__DIR__ . '/../../Assets/Functions.php');
    }

    public function testWillThrowExceptionWhenFileExists(): void
    {
        $path = 'path/to/file.txt';
        $this->filesystem->write($path, '1');

        $filter = new RenameUpload([
            'target' => $path,
            'overwrite' => false,
            'filesystem' => $this->filesystem,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("File 'path/to/file.txt' could not be uploaded. It already exists.");
        $filter->filter(__DIR__ . '/../../Assets/test.txt');
    }

    public function testWillThrowExceptionWhenFilesystemFails(): void
    {
        $this->filesystem = $this->prophesize(FilesystemOperator::class);
        $this->filesystem->writeStream(self::PATH, Argument::any())
            ->willThrow(UnableToWriteFile::atLocation(''))
            ->shouldBeCalled();
        $this->filesystem->fileExists(self::PATH)
            ->willReturn(false);
        $this->filesystem = $this->filesystem->reveal();

        $value = __DIR__ . '/../../Assets/test.txt';
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            "File '%s' could not be uploaded. An error occurred while processing the file.",
            $value,
        ));

        $this->filter()->filter($value);
    }

    private function filter(): RenameUpload
    {
        return new RenameUpload([
            'target' => 'path/to/file.txt',
            'filesystem' => $this->filesystem,
        ]);
    }

}

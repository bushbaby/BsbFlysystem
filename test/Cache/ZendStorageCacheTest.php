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

namespace BsbFlysystemTest\Cache;

use BsbFlysystem\Cache\ZendStorageCache;
use PHPUnit\Framework\TestCase;

class ZendStorageCacheTest extends TestCase
{
    protected function setUp(): void
    {
        if (! \class_exists('Laminas\Cache\Storage\StorageInterface')) {
            $this->markTestSkipped('laminas/laminas-cache not required');
        }
    }

    public function testLoadDefault(): void
    {
        $mock = $this->getMockBuilder('Laminas\Cache\Storage\StorageInterface')->getMock();
        $mock->expects($this->once())->method('getItem');

        $zendStorageCache = new ZendStorageCache($mock);
        $zendStorageCache->load();
    }

    public function testLoadDefaultCallsSetFromStorage(): void
    {
        $mock = $this->getMockBuilder('Laminas\Cache\Storage\StorageInterface')->getMock();
        // setFromStorage expects json in this form
        $mock->expects($this->once())->method('getItem')->willReturn('this-is-not-valid-json');

        $zendStorageCache = new ZendStorageCache($mock);
        $zendStorageCache->load();

        $this->assertTrue(JSON_ERROR_NONE !== \json_last_error());
    }

    public function testLoadWithCustomKey(): void
    {
        $mock = $this->getMockBuilder('Laminas\Cache\Storage\StorageInterface')->getMock();
        $mock->expects($this->once())->method('getItem')->with('akey');
        $zendStorageCache = new ZendStorageCache($mock, 'akey');

        $zendStorageCache->load();
    }

    public function testSaveDefault(): void
    {
        $mock = $this->getMockBuilder('Laminas\Cache\Storage\StorageInterface')->getMock();
        $mock->expects($this->once())->method('setItem');

        $zendStorageCache = new ZendStorageCache($mock);
        $zendStorageCache->save();
    }

    public function testSaveWithCustomKey(): void
    {
        $mock = $this->getMockBuilder('Laminas\Cache\Storage\StorageInterface')->getMock();
        $mock->expects($this->once())->method('setItem')->with('akey');

        $zendStorageCache = new ZendStorageCache($mock, 'akey');
        $zendStorageCache->save();
    }
}

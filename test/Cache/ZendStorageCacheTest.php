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

namespace BsbFlysystemTest\Cache;

use BsbFlysystem\Cache\ZendStorageCache;
use BsbFlysystemTest\Framework\TestCase;

class ZendStorageCacheTest extends TestCase
{
    public function testLoadDefault()
    {
        $mock = $this->getMockBuilder('Zend\Cache\Storage\StorageInterface')->getMock();
        $mock->expects($this->once())->method('getItem');

        $zendStorageCache = new ZendStorageCache($mock);
        $zendStorageCache->load();
    }

    public function testLoadDefaultCallsSetFromStorage()
    {
        $mock = $this->getMockBuilder('Zend\Cache\Storage\StorageInterface')->getMock();
        // setFromStorage expects json in this form
        $mock->expects($this->once())->method('getItem')->willReturn('this-is-not-valid-json');

        $zendStorageCache = new ZendStorageCache($mock);
        $zendStorageCache->load();

        $this->assertTrue(JSON_ERROR_NONE !== \json_last_error());
    }

    public function testLoadWithCustomKey()
    {
        $mock = $this->getMockBuilder('Zend\Cache\Storage\StorageInterface')->getMock();
        $mock->expects($this->once())->method('getItem')->with('akey');
        $zendStorageCache = new ZendStorageCache($mock, 'akey');

        $zendStorageCache->load();
    }

    public function testSaveDefault()
    {
        $mock = $this->getMockBuilder('Zend\Cache\Storage\StorageInterface')->getMock();
        $mock->expects($this->once())->method('setItem');

        $zendStorageCache = new ZendStorageCache($mock);
        $zendStorageCache->save();
    }

    public function testSaveWithCustomKey()
    {
        $mock = $this->getMockBuilder('Zend\Cache\Storage\StorageInterface')->getMock();
        $mock->expects($this->once())->method('setItem')->with('akey');

        $zendStorageCache = new ZendStorageCache($mock, 'akey');
        $zendStorageCache->save();
    }
}

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

namespace BsbFlysystemTest\Service;

use BsbFlysystem\Exception\RuntimeException;
use BsbFlysystem\Service\AdapterManager;
use BsbFlysystemTest\Bootstrap;
use Laminas\ServiceManager\ServiceManager;
use League\Flysystem\PathPrefixing\PathPrefixedAdapter;
use League\Flysystem\ReadOnly\ReadOnlyFilesystemAdapter;
use PHPUnit\Framework\TestCase;

class AdapterManagerTest extends TestCase
{
    public function testCreateViaServiceManager(): void
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get(AdapterManager::class);

        $this->assertInstanceOf(AdapterManager::class, $manager);
    }

    public function testCreateByAliasViaServiceManager(): void
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $this->assertInstanceOf(AdapterManager::class, $manager);
    }

    public function testManagerValidatesPlugin(): void
    {
        $manager = new AdapterManager(new ServiceManager());
        $plugin = $this->getMockBuilder(\League\Flysystem\FilesystemAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $manager->validatePlugin($plugin);

        $this->expectException(RuntimeException::class);

        $plugin = new \stdClass();
        $manager->validatePlugin($plugin);
    }

    public function testCreateViaServiceManagerLocal(): void
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get(AdapterManager::class);

        $this->assertInstanceOf(\League\Flysystem\FilesystemAdapter::class, $manager->get('local_default'));
    }

    public function testWrapsPathPrefixedAdapter(): void
    {
        $sm = Bootstrap::getServiceManager();
        $adapter = $sm->get(AdapterManager::class)->get('local_default', ['prefix' => '/path']);

        $this->assertInstanceOf(PathPrefixedAdapter::class, $adapter);
    }

    public function testWrapsReadOnlyFilesystemAdapter(): void
    {
        $sm = Bootstrap::getServiceManager();
        $adapter = $sm->get(AdapterManager::class)->get('local_default', ['readonly' => true]);

        $this->assertInstanceOf(ReadOnlyFilesystemAdapter::class, $adapter);
    }
}

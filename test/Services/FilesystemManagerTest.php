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

use BsbFlysystem\Service\FilesystemManager;
use BsbFlysystemTest\Bootstrap;
use Laminas\ServiceManager\ServiceManager;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

class FilesystemManagerTest extends TestCase
{
    public function testCreateViaServiceManager(): void
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get(FilesystemManager::class);

        $this->assertInstanceOf(FilesystemManager::class, $manager);
    }

    public function testCreateByAliasViaServiceManager(): void
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemManager');

        $this->assertInstanceOf(FilesystemManager::class, $manager);
    }

    public function testManagerValidatesPlugin(): void
    {
        $manager = new FilesystemManager(new ServiceManager());
        $plugin = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $manager->validatePlugin($plugin);

        $this->expectException('RuntimeException');

        $plugin = new \stdClass();
        $manager->validatePlugin($plugin);
    }

    public function testCanGetSpecificFilesystem(): void
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemManager');

        $this->assertInstanceOf(Filesystem::class, $manager->get('default'));
    }

    public function testServicesSharedByDefault(): void
    {
        $sm = Bootstrap::getServiceManager();

        /** @var FilesystemManager $manager */
        $manager = $sm->get(FilesystemManager::class);

        $localA = $manager->get('default');
        $localB = $manager->get('default');
        $this->assertSame($localA, $localB);
    }

    public function testConfigurationOverrideableForNotSharedServices(): void
    {
        $sm = Bootstrap::getServiceManager();

        /** @var FilesystemManager $manager */
        $manager = $sm->get(FilesystemManager::class);

        /** @var Filesystem $filesystem */
        $localA = $manager->get('default');

        /** @var Filesystem $filesystem */
        $localB = $manager->get('default', [
            'adapter_options' => ['location' => './test/_build/documents'],
        ]);

        $localA->write('/test-from-fs1.txt', '');
        $localB->write('/test-from-fs2.txt', '');

        $this->assertNotSame($localA, $localB);
        $this->assertFalse($localA->has('/test-from-fs2.txt'));
        $this->assertFalse($localB->has('/test-from-fs1.txt'));
    }
}

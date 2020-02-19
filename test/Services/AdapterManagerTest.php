<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2020 bushbaby multimedia. (https://bushbaby.nl)
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
use PHPUnit\Framework\TestCase;
use stdClass;

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
        $plugin = $this->getMockBuilder(\League\Flysystem\Adapter\AbstractAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $manager->validatePlugin($plugin);

        $this->expectException(RuntimeException::class);

        $plugin = new stdClass();
        $manager->validatePlugin($plugin);
    }

    public function testCreateViaServiceManagerLocal(): void
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $this->assertInstanceOf(\League\Flysystem\Adapter\AbstractAdapter::class, $manager->get('local_default'));
    }

    public function testCreateViaServiceManagerNull(): void
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get(AdapterManager::class);

        $this->assertInstanceOf(\League\Flysystem\Adapter\NullAdapter::class, $manager->get('null_default'));
    }
}

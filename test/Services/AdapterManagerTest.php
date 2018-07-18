<?php

declare(strict_types=1);

namespace BsbFlysystemTest\Service;

use BsbFlysystem\Service\AdapterManager;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;
use Zend\ServiceManager\ServiceManager;

class AdapterManagerTest extends TestCase
{
    public function testCreateViaServiceManager()
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get(\BsbFlysystem\Service\AdapterManager::class);

        $this->assertInstanceOf(\BsbFlysystem\Service\AdapterManager::class, $manager);
    }

    public function testCreateByAliasViaServiceManager()
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $this->assertInstanceOf(\BsbFlysystem\Service\AdapterManager::class, $manager);
    }

    public function testManagerValidatesPlugin()
    {
        $manager = new AdapterManager(new ServiceManager());
        $plugin = $this->getMockBuilder('League\Flysystem\Adapter\AbstractAdapter')
            ->disableOriginalConstructor()
            ->getMock();

        $manager->validatePlugin($plugin);

        $this->expectException(\BsbFlysystem\Exception\RuntimeException::class);

        $plugin = new \stdClass();
        $manager->validatePlugin($plugin);
    }

    public function testCreateViaServiceManagerLocal()
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $this->assertInstanceOf('League\Flysystem\Adapter\AbstractAdapter', $manager->get('local_default'));
    }

    public function testCreateViaServiceManagerNull()
    {
        $sm = Bootstrap::getServiceManager();
        $manager = $sm->get(\BsbFlysystem\Service\AdapterManager::class);

        $this->assertInstanceOf('League\Flysystem\Adapter\NullAdapter', $manager->get('null_default'));
    }
}

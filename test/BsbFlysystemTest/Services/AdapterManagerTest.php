<?php

namespace BsbFlysystemTest\Service;

use BsbFlysystem\Service\AdapterManager;
use BsbFlysystem\Service\ConnectionManager;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;

class AdapterManagerTest extends TestCase
{

    public function testCreateViaServiceManager()
    {
        $sm      = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $this->assertInstanceOf('BsbFlysystem\Service\AdapterManager', $manager);
    }

    public function testManagerValidatesPlugin()
    {
        $manager = new AdapterManager();
        $plugin  = $this->getMockBuilder('League\Flysystem\Adapter\AbstractAdapter')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertNull($manager->validatePlugin($plugin));

        $this->setExpectedException('Zend\ServiceManager\Exception\RuntimeException');

        $plugin = new \stdClass();
        $this->assertNull($manager->validatePlugin($plugin));
    }

    public function testCreateViaServiceManagerLocal()
    {
        $sm      = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $this->assertInstanceOf('League\Flysystem\Adapter\AbstractAdapter', $manager->get('local_default'));
    }

    public function testServicesSharedByDefault()
    {
        $sm = Bootstrap::getServiceManager();
        /** @var AbstractPluginManager $manager */
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $localA = $manager->get('local_data');
        $localB = $manager->get('local_data');
        $this->assertTrue($localA === $localB);
    }
}

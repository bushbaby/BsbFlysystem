<?php

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\VfsAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;

class VfsAdapterFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $sm      = Bootstrap::getServiceManager();
        $manager = $sm->get('BsbFlysystemAdapterManager');

        $factory = new VfsAdapterFactory();

        $adapter = $factory->createService($manager, null, null);

        $this->assertInstanceOf('League\Flysystem\Vfs\VfsAdapter', $adapter);
    }
}

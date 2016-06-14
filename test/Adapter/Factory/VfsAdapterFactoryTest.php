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
        $factory = new VfsAdapterFactory();

        $adapter = $factory($sm, null);

        $this->assertInstanceOf('League\Flysystem\Vfs\VfsAdapter', $adapter);
    }
}

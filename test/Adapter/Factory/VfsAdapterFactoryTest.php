<?php

declare(strict_types=1);

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\VfsAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;
use League\Flysystem\Vfs\VfsAdapter;

class VfsAdapterFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $sm = Bootstrap::getServiceManager();
        $factory = new VfsAdapterFactory();

        $adapter = $factory($sm, null);

        $this->assertInstanceOf(VfsAdapter::class, $adapter);
    }
}

<?php

declare(strict_types=1);

namespace BsbFlysystemTest;

use BsbFlysystem\Module;
use BsbFlysystemTest\Framework\TestCase;

class ModuleTest extends TestCase
{
    public function testModuleGetConfig()
    {
        $module = new Module();

        $this->assertNotEmpty($module->getConfig());
    }
}

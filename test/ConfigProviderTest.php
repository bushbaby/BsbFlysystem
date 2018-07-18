<?php

declare(strict_types=1);

namespace BsbFlysystemTest;

use BsbFlysystem\Module;
use BsbFlysystemTest\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    public function testConfigProviderGetConfig()
    {
        $config = (new \BsbFlysystem\ConfigProvider())();

        $this->assertNotEmpty($config);
    }

    public function testConfigEqualsToModuleConfig()
    {
        $moduleConfig = (new Module())->getConfig();
        $config = (new \BsbFlysystem\ConfigProvider())();

        $this->assertEquals($moduleConfig['service_manager'], $config['dependencies']);
        $this->assertEquals($moduleConfig['bsb_flysystem'], $config['bsb_flysystem']);
    }
}

<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2019 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

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

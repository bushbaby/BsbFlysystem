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

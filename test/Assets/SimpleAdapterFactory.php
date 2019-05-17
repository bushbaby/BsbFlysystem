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

namespace BsbFlysystemTest\Assets;

use BsbFlysystem\Adapter\Factory\AbstractAdapterFactory;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\AdapterInterface;
use Psr\Container\ContainerInterface;

class SimpleAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        $this->mergeMvcConfig($container, \func_get_arg(2));

        $this->validateConfig();

        return new NullAdapter();
    }

    public function validateConfig()
    {
    }
}

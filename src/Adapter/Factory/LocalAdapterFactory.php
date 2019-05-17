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

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\AdapterInterface;
use Psr\Container\ContainerInterface;

class LocalAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        $adapter = new Adapter($this->options['root']);

        return $adapter;
    }

    protected function validateConfig()
    {
        if (! isset($this->options['root'])) {
            throw new UnexpectedValueException("Missing 'root' as option");
        }
    }
}

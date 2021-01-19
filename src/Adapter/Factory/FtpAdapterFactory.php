<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2021 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\Adapter\Ftp as Adapter;
use League\Flysystem\AdapterInterface;
use Psr\Container\ContainerInterface;

class FtpAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        return new Adapter($this->options);
    }

    protected function validateConfig(): void
    {
        if (! isset($this->options['host'])) {
            throw new UnexpectedValueException("Missing 'host' as option");
        }

        if (! isset($this->options['port'])) {
            throw new UnexpectedValueException("Missing 'port' as option");
        }

        if (! isset($this->options['username'])) {
            throw new UnexpectedValueException("Missing 'username' as option");
        }

        if (! isset($this->options['password'])) {
            throw new UnexpectedValueException("Missing 'password' as option");
        }
    }
}

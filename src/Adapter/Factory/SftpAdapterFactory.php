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

use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Sftp\SftpAdapter as Adapter;
use Psr\Container\ContainerInterface;

class SftpAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        if (! \class_exists(Adapter::class)) {
            throw new RequirementsException(
                ['league/flysystem-sftp'],
                'Sftp'
            );
        }

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

        if (! isset($this->options['password']) && ! isset($this->options['privateKey'])) {
            throw new UnexpectedValueException("Missing either 'password' or 'privateKey' as option");
        }
    }
}

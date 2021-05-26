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

use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\AdapterInterface;
use Psr\Container\ContainerInterface;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter as Adapter;

class DropboxAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        if (! \class_exists(Adapter::class)) {
            throw new RequirementsException(['spatie/flysystem-dropbox'], 'Dropbox');
        }

        $client = new Client(
            $this->options['access_token']
        );

        return new Adapter($client, $this->options['prefix']);
    }

    protected function validateConfig(): void
    {
        if (! isset($this->options['access_token'])) {
            throw new UnexpectedValueException("Missing 'access_token' as option");
        }

        if (! isset($this->options['prefix'])) {
            $this->options['prefix'] = '';
        }
    }
}

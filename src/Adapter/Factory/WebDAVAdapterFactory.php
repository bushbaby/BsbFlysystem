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
use League\Flysystem\WebDAV\WebDAVAdapter as Adapter;
use Psr\Container\ContainerInterface;
use Sabre\DAV\Client;

class WebDAVAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        if (! \class_exists(\League\Flysystem\WebDAV\WebDAVAdapter::class)) {
            throw new RequirementsException(
                ['league/flysystem-webdav'],
                'WebDAV'
            );
        }

        $client = new Client($this->options);

        $adapter = new Adapter($client, $this->options['prefix']);

        return $adapter;
    }

    protected function validateConfig()
    {
        if (! isset($this->options['baseUri'])) {
            throw new UnexpectedValueException("Missing 'baseUri' as option");
        }

        if (! isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

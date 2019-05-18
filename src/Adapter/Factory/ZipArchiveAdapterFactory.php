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
use League\Flysystem\ZipArchive\ZipArchiveAdapter as Adapter;
use Psr\Container\ContainerInterface;

class ZipArchiveAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        if (! \class_exists(Adapter::class)) {
            throw new RequirementsException(
                ['league/ziparchive'],
                'ZipArchive'
            );
        }

        return new Adapter($this->options['archive'], null, $this->options['prefix']);
    }

    protected function validateConfig(): void
    {
        if (! isset($this->options['archive'])) {
            throw new UnexpectedValueException("Missing 'archive' as option");
        }

        if (! isset($this->options['prefix'])) {
            $this->options['prefix'] = null;
        }
    }
}

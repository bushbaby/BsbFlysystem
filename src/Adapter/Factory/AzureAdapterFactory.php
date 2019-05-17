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
use League\Flysystem\Azure\AzureAdapter as Adapter;
use Psr\Container\ContainerInterface;
use WindowsAzure\Common\ServicesBuilder;

class AzureAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        if (! \class_exists(\League\Flysystem\Azure\AzureAdapter::class)) {
            throw new RequirementsException(
                ['league/flysystem-azure'],
                'Azure'
            );
        }
        $endpoint = \sprintf(
            'DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s',
            $this->options['account-name'],
            $this->options['account-key']
        );

        $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($endpoint);
        $adapter = new Adapter($blobRestProxy, $this->options['container']);

        return $adapter;
    }

    protected function validateConfig()
    {
        if (! isset($this->options['account-name'])) {
            throw new UnexpectedValueException("Missing 'account-name' as option");
        }

        if (! isset($this->options['account-key'])) {
            throw new UnexpectedValueException("Missing 'account-key' as option");
        }

        if (! isset($this->options['container'])) {
            throw new UnexpectedValueException("Missing 'container' as option");
        }
    }
}

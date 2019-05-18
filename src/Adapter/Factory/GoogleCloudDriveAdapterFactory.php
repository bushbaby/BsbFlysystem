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
use Google\Cloud\Storage\StorageClient;
use League\Flysystem\AdapterInterface;
use Psr\Container\ContainerInterface;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter as Adapter;

class GoogleCloudDriveAdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): AdapterInterface
    {
        if (! \class_exists(Adapter::class)) {
            throw new RequirementsException(
                ['superbalist/flysystem-google-storage'],
                'GoogleCloudDrive'
            );
        }

        $storageClient = new StorageClient([
            'projectId' => $this->options['project_id'],
        ]);

        $bucket = $storageClient->bucket($this->options['bucket']);

        return new Adapter($storageClient, $bucket);
    }

    protected function validateConfig(): void
    {
        if (! isset($this->options['project_id'])) {
            throw new UnexpectedValueException("Missing 'project_id' as option");
        }

        if (! isset($this->options['bucket'])) {
            throw new UnexpectedValueException("Missing 'bucket' as option");
        }
    }
}

<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use Aws\S3\S3Client;
use BsbFlysystem\Exception\RequirementsException;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\FilesystemAdapter;
use Psr\Container\ContainerInterface;

class AwsS3v3AdapterFactory extends AbstractAdapterFactory
{
    public function doCreateService(ContainerInterface $container): FilesystemAdapter
    {
        if (! class_exists(AwsS3V3Adapter::class)) {
            throw new RequirementsException(['league/flysystem-aws-s3-v3'], 'AwsS3v3');
        }

        $clientConfig = $this->options['client'];

        if (! isset($this->options['iam']) || (isset($this->options['iam']) && (false === $this->options['iam']))) {
            $credentials = [
                'key' => $this->options['client']['credentials']['key'],
                'secret' => $this->options['client']['credentials']['secret'],
            ];
            $clientConfig = array_merge(compact('credentials'), $clientConfig);
        }

        $this->options['client'] = new S3Client($clientConfig);

        if (\array_key_exists('mimeTypeDetector', $this->options)) {
            $this->options['mimeTypeDetector'] = $container->get($this->options['mimeTypeDetector']);
        }

        if (\array_key_exists('visibility', $this->options)) {
            $this->options['visibility'] = $container->get($this->options['visibility']);
        }

        return new AwsS3V3Adapter(...$this->options);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function validateConfig(): void
    {
        // if (! isset($this->options['iam']) || (isset($this->options['iam']) && (false === $this->options['iam']))) {
        //     if (! isset($this->options['credentials']) || ! \is_array($this->options['credentials'])) {
        //         throw new UnexpectedValueException("Missing 'credentials' as array");
        //     }

        //     if (! isset($this->options['credentials']['key'])) {
        //         throw new UnexpectedValueException("Missing 'key' as option");
        //     }

        //     if (! isset($this->options['credentials']['secret'])) {
        //         throw new UnexpectedValueException("Missing 'secret' as option");
        //     }
        // }
    }
}

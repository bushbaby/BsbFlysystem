<?php

namespace BsbFlysystem\Adapter\Factory;

use Aws\S3\S3Client;
use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystem\Exception\UnexpectedValueException;
use League\Flysystem\AwsS3v3\AwsS3Adapter as Adapter;
use Zend\ServiceManager\ServiceLocatorInterface;

class AwsS3v3AdapterFactory extends AbstractAdapterFactory
{

    /**
     * @inheritdoc
     */
    public function doCreateService(ServiceLocatorInterface $serviceLocator)
    {
        if (!class_exists(\League\Flysystem\AwsS3v3\AwsS3Adapter::class)) {
            throw new RequirementsException(
                ['league/flysystem-aws-s3-v3'],
                'AwsS3v3'
            );
        }
        $config = [
            'region' => $this->options['region'],
            'version' => $this->options['version'],
            'request.options' => $this->options['request.options'],
        ];

        if (!isset($this->options['iam']) || (isset($this->options['iam']) && (false === $this->options['iam']))) {
            $credentials = [
                'key'    => $this->options['credentials']['key'],
                'secret' => $this->options['credentials']['secret'],
            ];
            $config = array_merge(compact('credentials'), $config);
        }

        $client = new S3Client($config);

        $adapter = new Adapter($client, $this->options['bucket'], $this->options['prefix']);

        return $adapter;
    }

    /**
     * @inheritdoc
     */
    protected function validateConfig()
    {
        if (!isset($this->options['iam']) || (isset($this->options['iam']) && (false === $this->options['iam']))) {
            if (!isset($this->options['credentials']) || !is_array($this->options['credentials'])) {
                throw new UnexpectedValueException("Missing 'credentials' as array");
            }

            if (!isset($this->options['credentials']['key'])) {
                throw new UnexpectedValueException("Missing 'key' as option");
            }

            if (!isset($this->options['credentials']['secret'])) {
                throw new UnexpectedValueException("Missing 'secret' as option");
            }
        }

        if (!isset($this->options['region'])) {
            throw new UnexpectedValueException("Missing 'region' as option");
        }

        if (!isset($this->options['bucket'])) {
            throw new UnexpectedValueException("Missing 'bucket' as option");
        }

        if (!isset($this->options['version'])) {
            $this->options['version'] = 'latest';
        }

        if (!isset($this->options['prefix'])) {
            $this->options['prefix'] = '';
        }

        if (!isset($this->options['request.options'])) {
            $this->options['request.options'] = [];
        }
    }
}

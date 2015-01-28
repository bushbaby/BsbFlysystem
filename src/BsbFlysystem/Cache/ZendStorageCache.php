<?php

namespace BsbFlysystem\Cache;

use League\Flysystem\Cached\Storage\AbstractCache;
use Zend\Cache\Storage\StorageInterface;

/**
 * Wrapper class that allows usage of a Zend Cache as a Flysystem Cache
 */
class ZendStorageCache extends AbstractCache
{

    /**
     * @var string storage key
     */
    protected $key;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage, $key = 'bsbflysystem')
    {
        $this->storage = $storage;
        $this->key     = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $contents = $this->storage->getItem($this->key);

        if ($contents !== null) {
            $this->setFromStorage($contents);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $contents = $this->getForStorage();
        $this->storage->setItem($this->key, $contents);
    }

}
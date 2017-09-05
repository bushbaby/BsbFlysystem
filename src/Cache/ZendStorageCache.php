<?php

declare(strict_types=1);

namespace BsbFlysystem\Cache;

use League\Flysystem\Cached\Storage\AbstractCache;
use Zend\Cache\Storage\StorageInterface;

/**
 * Wrapper class that allows usage of a Zend Cache as a Flysystem Cache.
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

    public function __construct(StorageInterface $storage, string $key = 'bsbflysystem')
    {
        $this->storage = $storage;
        $this->key     = $key;
    }

    public function load()
    {
        $contents = $this->storage->getItem($this->key);

        if ($contents !== null) {
            $this->setFromStorage($contents);
        }
    }

    public function save()
    {
        $contents = $this->getForStorage();
        $this->storage->setItem($this->key, $contents);
    }
}

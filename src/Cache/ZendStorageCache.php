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

namespace BsbFlysystem\Cache;

use League\Flysystem\Cached\Storage\AbstractCache;
use Laminas\Cache\Storage\StorageInterface;

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
        $this->key = $key;
    }

    public function load(): void
    {
        $contents = $this->storage->getItem($this->key);

        if (null !== $contents) {
            $this->setFromStorage($contents);
        }
    }

    public function save(): void
    {
        $contents = $this->getForStorage();
        $this->storage->setItem($this->key, $contents);
    }
}

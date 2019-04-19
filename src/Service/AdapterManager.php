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

namespace BsbFlysystem\Service;

use BsbFlysystem\Exception\RuntimeException;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

class AdapterManager extends AbstractPluginManager
{
    /**
     * @var string
     */
    protected $instanceOf = \League\Flysystem\AdapterInterface::class;

    /**
     * @var bool
     */
    protected $shareByDefault = true;

    /**
     * @var bool
     */
    protected $sharedByDefault = true;

    /**
     * @var array
     */
    protected $factories = [
        'League\Flysystem\Adapter\NullAdapter' => \Zend\ServiceManager\Factory\InvokableFactory::class,
        'leagueflysystemadapternulladapter' => \Zend\ServiceManager\Factory\InvokableFactory::class,
    ];

    public function validate($instance)
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new Exception\InvalidServiceException(\sprintf(
                'Invalid adapter "%s" created; not an instance of %s',
                \get_class($instance),
                $this->instanceOf
            ));
        }
    }

    public function validatePlugin($instance)
    {
        try {
            $this->validate($instance);
        } catch (Exception\InvalidServiceException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

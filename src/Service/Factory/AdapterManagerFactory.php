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

namespace BsbFlysystem\Service\Factory;

use BsbFlysystem\Service\AdapterManager;
use Psr\Container\ContainerInterface;

class AdapterManagerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AdapterManager
    {
        $config = ($container->has('config') ? $container->get('config') : [])['bsb_flysystem'] ?? [];
        $adapters = $config['adapters'] ?? [];
        $serviceConfig = $config['adapter_manager']['config'] ?? [];

        foreach ($adapters as $name => $adapterConfig) {
            \assert(
                \array_key_exists('factory', $adapterConfig),
                "Option 'factory' must be defined in an adapter configuration"
            );

            $serviceConfig['factories'][$name] = $adapterConfig['factory'];
        }

        return new AdapterManager($container, $serviceConfig);
    }
}

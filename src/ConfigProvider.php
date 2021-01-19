<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2021 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystem;

class ConfigProvider
{
    public function __invoke(): array
    {
        $config = (new Module())->getConfig();

        return [
            'dependencies' => $config['service_manager'],
            'bsb_flysystem' => $config['bsb_flysystem'],
        ];
    }
}

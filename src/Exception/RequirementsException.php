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

namespace BsbFlysystem\Exception;

use Exception;

class RequirementsException extends RuntimeException
{
    public function __construct(array $requirements, string $for, int $code = 0, Exception $previous = null)
    {
        $requirements = array_map(function ($r) {
            return sprintf("'%s'", trim($r));
        }, $requirements);

        $message = sprintf(
            "Install %s to use '%s'",
            implode(' & ', $requirements),
            $for
        );

        parent::__construct($message, $code, $previous);
    }
}

<?php

declare(strict_types=1);

namespace BsbFlysystem\Exception;

use Exception;

class RequirementsException extends RuntimeException
{
    /**
     * @param array     $requirements
     * @param int       $for
     * @param int       $code
     * @param Exception $previous
     */
    public function __construct(array $requirements, $for, $code = 0, Exception $previous = null)
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

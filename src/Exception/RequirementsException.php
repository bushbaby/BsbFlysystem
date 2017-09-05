<?php

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

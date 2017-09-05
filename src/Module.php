<?php

declare(strict_types=1);

namespace BsbFlysystem;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}

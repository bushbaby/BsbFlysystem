<?php

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

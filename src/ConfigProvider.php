<?php

namespace BsbFlysystem;

class ConfigProvider
{
    /**
     * Return configuration for this component.
     *
     * @return array
     */
    public function __invoke()
    {
        $config = (new Module())->getConfig();

        return [
            'dependencies' => $config['service_manager'],
            'bsb_flysystem' => $config['bsb_flysystem'],
        ];
    }
}

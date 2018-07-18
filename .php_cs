<?php

$config = new Bsb\CS\Config([
    'binary_operator_spaces' => [
        'default' => 'single_space',
    ],
]);

$config->getFinder()->in(__DIR__);

$cacheDir = getenv('TRAVIS') ? getenv('HOME') . '/.php-cs-fixer' : __DIR__;

$config->setCacheFile($cacheDir . '/.php_cs.cache');

return $config;

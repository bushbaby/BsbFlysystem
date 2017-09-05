<?php

declare(strict_types=1);

namespace BsbFlysystem\Filter\File
{
    function is_uploaded_file($filepath)
    {
        return realpath($filepath) === realpath(__DIR__ . '/test.txt');
    }
}

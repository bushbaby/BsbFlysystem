<?php

namespace BsbFlysystemTest\Exception;

use BsbFlysystem\Exception\RequirementsException;
use BsbFlysystemTest\Framework\TestCase;

class RequirementsExceptionTest extends TestCase
{
    public function testConstructorSetMessage()
    {
        $exception = new RequirementsException(['xxxx', 'yyyy'], 'zzzz');

        $this->assertEquals("Install 'xxxx' & 'yyyy' to use 'zzzz'", $exception->getMessage());
    }
}

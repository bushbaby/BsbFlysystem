<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014-2020 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystemTest\Exception;

use BsbFlysystem\Exception\RequirementsException;
use PHPUnit\Framework\TestCase;

class RequirementsExceptionTest extends TestCase
{
    public function testConstructorSetMessage(): void
    {
        $exception = new RequirementsException(['xxxx', 'yyyy'], 'zzzz');

        $this->assertEquals("Install 'xxxx' & 'yyyy' to use 'zzzz'", $exception->getMessage());
    }
}

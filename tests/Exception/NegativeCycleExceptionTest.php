<?php

namespace Graphp\Graph\Tests\Exception;

use Graphp\Graph\Exception\NegativeCycleException;
use Graphp\Graph\Tests\TestCase;

class NegativeCycleExceptionTest extends TestCase
{
    public function testConstructor()
    {
        $cycle = $this->getMockBuilder('Graphp\Graph\Walk')
                      ->disableOriginalConstructor()
                      ->getMock();

        $exception = new NegativeCycleException('test', 0, null, $cycle);

        $this->assertEquals('test', $exception->getMessage());
        $this->assertEquals($cycle, $exception->getCycle());
    }
}

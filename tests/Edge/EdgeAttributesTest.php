<?php

namespace Fhaculty\Graph\Tests\Edge;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Tests\TestCase;

class EdgeAttributesTest extends TestCase
{
    /**
     *
     * @var Edge
     */
    private $edge;

    /**
     * @before
     */
    public function setUpGraph()
    {

        $graph = new Graph();
        $graph->createVertex(1);
        $graph->createVertex(2);

        // 1 -> 2
        $this->edge = $graph->getVertex(1)->createEdge($graph->getVertex(2));
    }

    public function testCanSetFlowAndCapacity()
    {
        $this->edge->setCapacity(100);
        $this->edge->setFlow(10);

        $this->assertEquals(90, $this->edge->getCapacityRemaining());
    }

    public function testCanSetFlowBeforeCapacity()
    {
        $this->edge->setFlow(20);

        $this->assertEquals(null, $this->edge->getCapacityRemaining());
    }

    public function testFlowMustNotExceedCapacity()
    {
        $this->edge->setCapacity(20);

        $this->setExpectedException('RangeException');
        $this->edge->setFlow(100);
    }

    public function testCapacityMustBeGreaterThanFlow()
    {
        $this->edge->setFlow(100);

        $this->setExpectedException('RangeException');
        $this->edge->setCapacity(20);
    }

    public function testWeightMustBeNumeric()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->edge->setWeight("10");
    }

    public function testCapacityMustBeNumeric()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->edge->setCapacity("10");
    }

    public function testCapacityMustBePositive()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->edge->setCapacity(-10);
    }

    public function testFlowMustBeNumeric()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->edge->setFlow("10");
    }

    public function testFlowMustBePositive()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->edge->setFlow(-10);
    }
}

<?php

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Edge\Base as Edge;

class EdgeBaseTest extends TestCase
{

    /**
     *
     * @var Edge
     */
    private $edge;

    public function setUp()
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
    }

    public function testCanSetFlowBeforeCapacity()
    {
        $this->edge->setFlow(20);
    }

    /**
     * @expectedException RangeException
     */
    public function testFlowMustNotExceedCapacity()
    {
        $this->edge->setCapacity(20);
        $this->edge->setFlow(100);
    }
}

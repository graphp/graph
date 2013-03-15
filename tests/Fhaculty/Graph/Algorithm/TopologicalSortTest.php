<?php

use Fhaculty\Graph\Algorithm\TopologicalSort;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Graph;

class TopologicalSortTest extends TestCase
{
    public function testGraphEmpty()
    {
        $graph = new Graph();

        $alg = new TopologicalSort($graph);

        $this->assertSame(array(), $alg->getVertices());
    }

    public function testGraphIsolated()
    {
        $graph = new Graph();
        $graph->createVertex(1);
        $graph->createVertex(2);

        $alg = new TopologicalSort($graph);

        $this->assertSame(array(1 => $graph->getVertex(1), 2 => $graph->getVertex(2)), $alg->getVertices());
    }

    public function testGraphSimple()
    {
        $graph = new Graph();
        $graph->createVertex(1)->createEdgeTo($graph->createVertex(2));

        $alg = new TopologicalSort($graph);

        $this->assertSame(array(1 => $graph->getVertex(1), 2 => $graph->getVertex(2)), $alg->getVertices());
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testFailUndirected()
    {
        $graph = new Graph();
        $graph->createVertex(1)->createEdge($graph->createVertex(2));

        $alg = new TopologicalSort($graph);
        $alg->getVertices();
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testFailLoop()
    {
        $graph = new Graph();
        $graph->createVertex(1)->createEdgeTo($graph->getVertex(1));

        $alg = new TopologicalSort($graph);
        $alg->getVertices();
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testFailCycle()
    {
        $graph = new Graph();
        $graph->createVertex(1)->createEdgeTo($graph->createVertex(2));
        $graph->getVertex(2)->createEdgeTo($graph->getVertex(1));

        $alg = new TopologicalSort($graph);
        $alg->getVertices();
    }
}

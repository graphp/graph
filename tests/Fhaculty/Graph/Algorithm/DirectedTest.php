<?php

use Fhaculty\Graph\Algorithm\Directed as AlgorithmDirected;
use Fhaculty\Graph\Graph;

class DirectedTest extends TestCase
{
    public function testGraphEmpty()
    {
        $graph = new Graph();

        $alg = new AlgorithmDirected($graph);

        $this->assertFalse($alg->isDirected());
    }

    public function testGraphUndirected()
    {
        // 1 -- 2
        $graph = new Graph();
        $graph->createVertex(1)->createEdge($graph->createVertex(2));

        $alg = new AlgorithmDirected($graph);

        $this->assertFalse($alg->isDirected());
    }

    public function testGraphDirected()
    {
        // 1 -> 2
        $graph = new Graph();
        $graph->createVertex(1)->createEdgeTo($graph->createVertex(2));

        $alg = new AlgorithmDirected($graph);

        $this->assertTrue($alg->isDirected());
    }

    public function testGraphMixed()
    {
        // 1 -- 2 -> 3
        $graph = new Graph();
        $graph->createVertex(1)->createEdge($graph->createVertex(2));
        $graph->getVertex(2)->createEdgeTo($graph->createVertex(3));

        $alg = new AlgorithmDirected($graph);

        $this->assertTrue($alg->isDirected());
    }
}

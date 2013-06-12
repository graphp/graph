<?php

use Fhaculty\Graph\Algorithm\Flow as AlgorithmFlow;
use Fhaculty\Graph\Graph;

class FlowaTest extends TestCase
{
    public function testGraphEmpty()
    {
        $graph = new Graph();

        $alg = new AlgorithmFlow($graph);

        $this->assertFalse($alg->hasFlow());

        return $graph;
    }

    public function testEdgeWithZeroFlowIsConsideredFlow()
    {
        // 1 -- 2
        $graph = new Graph();
        $graph->createVertex(1)->createEdge($graph->createVertex(2))->setFlow(0);


        $alg = new AlgorithmFlow($graph);

        $this->assertTrue($alg->hasFlow());
    }

    /**
     *
     * @param Graph $graph
     * @depends testGraphEmpty
     */
    public function testGraphSimple(Graph $graph)
    {
        // 1 -> 2
        $graph->createVertex(1)->createEdgeTo($graph->createVertex(2));

        $alg = new AlgorithmFlow($graph);

        $this->assertFalse($alg->hasFlow());

        return $graph;
    }

    /**
     *
     * @param Graph $graph
     * @depends testGraphSimple
     */
    public function testGraphWithUnweightedEdges(Graph $graph)
    {
        // additional flow edge: 2 -> 3
        $graph->getVertex(2)->createEdgeTo($graph->createVertex(3))->setFlow(10);

        $alg = new AlgorithmFlow($graph);

        $this->assertTrue($alg->hasFlow());
    }
}

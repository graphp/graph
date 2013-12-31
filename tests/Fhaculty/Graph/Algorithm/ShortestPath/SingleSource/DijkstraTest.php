<?php

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Algorithm\ShortestPath\SingleSource\Dijkstra;

class DijkstraTest extends BaseShortestPathTest
{
    protected function createAlg()
    {
        return new Dijkstra();
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testGraphParallelNegative()
    {
        // 1 -[10]-> 2
        // 1 -[-1]-> 2
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $e1 = $v1->createEdgeTo($v2)->setWeight(10);
        $e2 = $v1->createEdgeTo($v2)->setWeight(-1);

        $this->createResult($v1);
    }
}

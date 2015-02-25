<?php

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Algorithm\ShortestPath\SingleSource\BreadthFirst;

class BreadthFirstTest extends BaseShortestPathTest
{
    protected function createAlg()
    {
        return new BreadthFirst();
    }

    public function testGraphParallelNegative()
    {
        // 1 -[10]-> 2
        // 1 -[-1]-> 2
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $e1 = $v1->createEdgeTo($v2)->setWeight(10);
        $e2 = $v1->createEdgeTo($v2)->setWeight(-1);

        $result = $this->createResult($v1);

        $this->assertEquals(1, $result->getDistanceTo($v2));
        $this->assertEquals(array(2 => 1), $result->getDistanceMap());
        $this->assertEquals(array($e1), $result->getEdges()->getVector());
        $this->assertEquals(array($e1), $result->getEdgesTo($v2)->getVector());
        $this->assertEquals(array(2 => $v2), $result->getVertices()->getMap());
        $this->assertEquals(array(2), $result->getVertices()->getIds());
    }

    protected function getExpectedWeight($edges)
    {
        return count($edges);
    }
}

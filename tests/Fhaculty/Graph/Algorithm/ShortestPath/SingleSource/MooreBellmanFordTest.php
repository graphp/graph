<?php

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Algorithm\ShortestPath\SingleSource\MooreBellmanFord;

class MooreBellmanFordTest extends BaseShortestPathTest
{
    protected function createAlg()
    {
        return new MooreBellmanFord();
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

        $alg = $this->createAlg();
        $result = $alg->createResult($v1);

        // $this->assertEquals(0, $alg->getDistance($v1));
        $this->assertEquals(-1, $result->getDistanceTo($v2));
        $this->assertEquals(array(2 => -1), $result->getDistanceMap());
        $this->assertEquals(array($e2), $result->getEdges()->getVector());
        //$this->assertEquals(array(), $alg->getEdgesTo($v1));
        $this->assertEquals(array($e2), $result->getEdgesTo($v2)->getVector());
        $this->assertEquals(array(2 => $v2), $result->getVertices()->getMap());
        $this->assertEquals(array(2), $result->getVertices()->getIds());
    }

    /**
     * @expectedException UnderflowException
     */
    public function testNoNegativeCycle()
    {
        // 1 -[10]-> 2
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $e1 = $v1->createEdgeTo($v2)->setWeight(10);

        $alg = $this->createAlg();
        $alg->getCycleNegative($v1);
    }

    public function testUndirectedNegativeWeightIsCycle()
    {
        // 1 -[-10]- 2
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $e1 = $v1->createEdge($v2)->setWeight(-10);

        $alg = $this->createAlg();

        $cycle = $alg->getCycleNegative($v1);
    }

    public function testLoopNegativeWeightIsCycle()
    {
        // 1 -[-10]-> 1
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $e1 = $v1->createEdge($v1)->setWeight(-10);

        $alg = $this->createAlg();

        $cycle = $alg->getCycleNegative($v1);
    }
}

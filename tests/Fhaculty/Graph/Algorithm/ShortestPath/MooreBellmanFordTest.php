<?php

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Algorithm\ShortestPath\MooreBellmanFord;

class MooreBellmanFordTest extends BaseShortestPathTest
{
    protected function createAlg(Vertex $vertex)
    {
        return new MooreBellmanFord($vertex);
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

        $alg = $this->createAlg($v1);

        // $this->assertEquals(0, $alg->getDistance($v1));
        $this->assertEquals(-1, $alg->getDistance($v2));
        $this->assertEquals(array(2 => -1), $alg->getDistanceMap());
        $this->assertEquals(array($e2), $alg->getEdges()->getVector());
        //$this->assertEquals(array(), $alg->getEdgesTo($v1));
        $this->assertEquals(array($e2), $alg->getEdgesTo($v2)->getVector());
        $this->assertEquals(array(2 => $v2), $alg->getVertices()->getMap());
        $this->assertEquals(array(2), $alg->getVertices()->getIds());

        return $alg;
    }

    /**
     * @param MooreBellmanFord $alg
     * @depends testGraphParallelNegative
     * @expectedException UnderflowException
     */
    public function testNoNegativeCycle(MooreBellmanFord $alg)
    {
        $alg->getCycleNegative();
    }

    public function testUndirectedNegativeWeightIsCycle()
    {
        // 1 -[-10]- 2
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $e1 = $v1->createEdge($v2)->setWeight(-10);

        $alg = $this->createAlg($v1);

        $cycle = $alg->getCycleNegative();
    }

    public function testLoopNegativeWeightIsCycle()
    {
        // 1 -[-10]-> 1
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $e1 = $v1->createEdge($v1)->setWeight(-10);

        $alg = $this->createAlg($v1);

        $cycle = $alg->getCycleNegative();
    }
}

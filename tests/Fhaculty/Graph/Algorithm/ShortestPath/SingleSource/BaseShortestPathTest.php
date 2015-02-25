<?php

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Algorithm\ShortestPath\SingleSource\Base as ShortestPathAlg;

abstract class BaseShortestPathTest extends TestCase
{
    /**
     * @return ShortestPathAlg
     */
    abstract protected function createAlg();

    /**
     *
     * @param Vertex $vertex
     * @return \Fhaculty\Graph\Algorithm\ShortestPath\SingleSource\Result
     */
    protected function createResult(Vertex $vertex)
    {
        return $this->createAlg()->createResult($vertex);
    }

    abstract public function testGraphParallelNegative();

    public function testGraphTrivial()
    {
        // 1
        $graph = new Graph();
        $v1 = $graph->createVertex(1);

        $result = $this->createResult($v1);

        $this->assertFalse($result->hasWalkTo($v1));
        //$this->assertEquals(0, $alg->getDistance($v1));
        $this->assertEquals(array(), $result->getDistanceMap());
        $this->assertEquals(array(), $result->getEdges()->getVector());
        //$this->assertEquals(array(), $alg->getEdgesTo($v1));
        $this->assertEquals(array(), $result->getVertices()->getVector());
        $this->assertEquals(array(), $result->getVertices()->getIds());

        $clone = $result->createGraph();
        $this->assertGraphEquals($graph,$clone);
    }

    public function testGraphSingleLoop()
    {
        // 1 -[4]> 1
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $e1 = $v1->createEdgeTo($v1)->setWeight(4);

        $result = $this->createResult($v1);

        $this->assertEquals(array($e1), $result->getEdges()->getVector());

        $expectedWeight = $this->getExpectedWeight(array($e1));
        $this->assertTrue($result->hasWalkTo($v1));
        $this->assertEquals($expectedWeight, $result->getDistanceTo($v1));
        $this->assertEquals(array(1 => $expectedWeight), $result->getDistanceMap());
        $this->assertEquals(array($e1), $result->getEdgesTo($v1)->getVector());
        $this->assertEquals(array(1 => $v1), $result->getVertices()->getMap());
        $this->assertEquals(array(1), $result->getVertices()->getIds());
    }

    public function testGraphCycle()
    {
        // 1 -[4]-> 2 -[2]-> 1
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $e1 = $v1->createEdgeTo($v2)->setWeight(4);
        $e2 = $v2->createEdgeTo($v1)->setWeight(2);

        $result = $this->createResult($v1);

        //$this->assertEquals(array($e2, $e1), $alg->getEdges());

        $expectedWeight = $this->getExpectedWeight(array($e1));
        $this->assertTrue($result->hasWalkTo($v2));
        $this->assertEquals(array($e1), $result->getEdgesTo($v2)->getVector());
        $this->assertEquals($expectedWeight, $result->getDistanceTo($v2));

        $expectedWeight = $this->getExpectedWeight(array($e1, $e2));
        $this->assertTrue($result->hasWalkTo($v1));
        $this->assertEquals(array($e1, $e2), $result->getEdgesTo($v1)->getVector());
        $this->assertEquals($expectedWeight, $result->getDistanceTo($v1));

        $walk = $result->getWalkTo($v1);
        $this->assertEquals(2, count($walk->getEdges()));
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testIsolatedVertexIsNotReachable()
    {
        // 1, 2
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);

        $result = $this->createResult($v1);

        $this->assertFalse($result->hasWalkTo($v2));

        $result->getEdgesTo($v2);
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testSeparateGraphsAreNotReachable()
    {
        // 1
        $graph1 = new Graph();
        $vg1 = $graph1->createVertex(1);

        $graph2 = new Graph();
        $vg2 = $graph2->createVertex(1);

        $result = $this->createResult($vg1);

        $result->getEdgesTo($vg2);
    }

    public function testGraphTwoComponents()
    {
        // 1 -[10]-> 2
        // 3 -[20]-> 4
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $v3 = $graph->createVertex(3);
        $v4 = $graph->createVertex(4);
        $e1 = $v1->createEdgeTo($v2)->setWeight(10);
        $e2 = $v3->createEdgeTo($v4)->setWeight(20);

        $result = $this->createResult($v1);

        $expectedWeight = $this->getExpectedWeight(array($e1));
        $this->assertEquals($expectedWeight, $result->getDistanceTo($v2));
        $this->assertEquals(array(2 => $expectedWeight), $result->getDistanceMap());
        $this->assertEquals(array($e1), $result->getEdges()->getVector());
        // $this->assertEquals(array(), $alg->getEdgesTo($v1));
        $this->assertEquals(array($e1), $result->getEdgesTo($v2)->getVector());
        $this->assertEquals(array(2 => $v2), $result->getVertices()->getMap());
        $this->assertEquals(array(2), $result->getVertices()->getIds());
    }

    public function testUndirectedPair()
    {
        // 1 --[10]-- 2
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $e1 = $v1->createEdge($v2)->setWeight(10);

        // test from vertex 1
        $result = $this->createResult($v1);
        $this->assertEquals($this->getExpectedWeight(array($e1)), $result->getDistanceTo($v2));
        $this->assertEquals(array($e1), $result->getEdgesTo($v2)->getVector());

        // test automatic cycle (1-2-1) due to undirected edges
        $this->assertEquals($this->getExpectedWeight(array($e1, $e1)), $result->getDistanceTo($v1));
        $this->assertEquals(array($e1, $e1), $result->getEdgesTo($v1)->getVector());

        // test from vertex 2
        $result = $this->createResult($v2);
        $this->assertEquals($this->getExpectedWeight(array($e1)), $result->getDistanceTo($v1));
        $this->assertEquals(array($e1), $result->getEdgesTo($v1)->getVector());
    }

    public function testUndirectedLine()
    {
        // 1 --[10]-- 2 --[20]-- 3
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $v3 = $graph->createVertex(3);
        $e1 = $v1->createEdge($v2)->setWeight(10);
        $e2 = $v2->createEdge($v3)->setWeight(20);

        $result = $this->createResult($v1);
        $this->assertEquals($this->getExpectedWeight(array($e1)), $result->getDistanceTo($v2));
        $this->assertEquals($this->getExpectedWeight(array($e1, $e2)), $result->getDistanceTo($v3));
        $this->assertEquals(array($e1), $result->getEdgesTo($v2)->getVector());
        $this->assertEquals(array($e1, $e2), $result->getEdgesTo($v3)->getVector());

        $result = $this->createResult($v2);
        $this->assertEquals($this->getExpectedWeight(array($e1)), $result->getDistanceTo($v1));
        $this->assertEquals($this->getExpectedWeight(array($e2)), $result->getDistanceTo($v3));
        $this->assertEquals(array($e1), $result->getEdgesTo($v1)->getVector());
        $this->assertEquals(array($e2), $result->getEdgesTo($v3)->getVector());

        // automatic cycle (2-1-2)
        $this->assertEquals(array($e1, $e1), $result->getEdgesTo($v2)->getVector());
    }

    public function testUndirectedSimpleGraph()
    {
        // 1 --[10]-- 2 --[20]-- 3
        // \                     /
        //  \----------[20]-----/
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $v3 = $graph->createVertex(3);
        $e1 = $v1->createEdgeTo($v2)->setWeight(10);
        $e2 = $v2->createEdgeTo($v3)->setWeight(20);
        $e3 = $v1->createEdgeTo($v3)->setWeight(20);

        $result = $this->createResult($v1);

        $expectedWeight = $this->getExpectedWeight(array($e3));
        $this->assertEquals($expectedWeight, $result->getDistanceTo($v3));
    }

    public function testUndirectedBugReport()
    {
        // 1 --[4]-- 2 --[4]-- 5 --[4]-- 6
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $v6 = $graph->createVertex(6);
        $v5 = $graph->createVertex(5);

        $e1 = $v1->createEdge($v2)->setWeight(4);
        $e2 = $v2->createEdge($v5)->setWeight(4);
        $e3 = $v5->createEdge($v6)->setWeight(4);

        $expectedWeight = $this->getExpectedWeight(array($e1, $e2, $e3));
        $this->assertEquals($expectedWeight, $this->createResult($v1)->getDistanceTo($v6));
    }

    protected function getExpectedWeight($edges)
    {
        $sum = 0;
        foreach ($edges as $edge) {
            $sum += $edge->getWeight();
        }
        return $sum;
    }
}

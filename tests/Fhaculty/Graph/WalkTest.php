<?php

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Walk;
use Fhaculty\Graph\Algorithm\Property\WalkProperty;

class WalkTest extends TestCase
{
    /**
     * @expectedException UnderflowException
     */
    public function testWalkCanNotBeEmpty()
    {
        Walk::factoryCycleFromVertices(array());
    }

    public function testWalkPath()
    {
        // 1 -- 2 -- 3
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $v3 = $graph->createVertex(3);
        $e1 = $v1->createEdgeTo($v2);
        $e2 = $v2->createEdgeTo($v3);

        $walk = Walk::factoryFromEdges(array($e1, $e2), $v1);

        $this->assertEquals(3, count($walk->getVertices()));
        $this->assertEquals(2, $walk->getNumberOfEdges());
        $this->assertSame($v1, $walk->getVertexSource());
        $this->assertSame($v3, $walk->getVertexTarget());
        $this->assertSame(array($v1, $e1, $v2, $e2, $v3), $walk->getAlternatingSequence());
        $this->assertTrue($walk->isValid());

        $graphClone = $walk->createGraph();
        $this->assertGraphEquals($graph, $graphClone);

        return $walk;
    }

    /**
     * @param Walk $walk
     * @depends testWalkPath
     */
    public function testWalkPathInvalidateByDestroyingVertex(Walk $walk)
    {
        // delete v3
        $walk->getVertexTarget()->destroy();

        $this->assertFalse($walk->isValid());
    }

    public function testWalkWithinGraph()
    {
        // 1 -- 2 -- 3
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $v3 = $graph->createVertex(3);
        $e1 = $v1->createEdgeTo($v2);
        $e2 = $v2->createEdgeTo($v3);

        // construct partial walk "1 -- 2"
        $walk = Walk::factoryFromEdges(array($e1), $v1);

        $this->assertEquals(2, count($walk->getVertices()));
        $this->assertEquals(1, $walk->getNumberOfEdges());
        $this->assertSame($v1, $walk->getVertexSource());
        $this->assertSame($v2, $walk->getVertexTarget());
        $this->assertSame(array($v1, $e1, $v2), $walk->getAlternatingSequence());
        $this->assertTrue($walk->isValid());

        $graphExpected = new Graph();
        $graphExpected->createVertex(1)->createEdgeTo($graphExpected->createVertex(2));

        $this->assertGraphEquals($graphExpected, $walk->createGraph());

        // construct same partial walk "1 -- 2"
        $walkVertices = Walk::factoryFromVertices(array($v1, $v2));

        $this->assertEquals(2, count($walkVertices->getVertices()));
        $this->assertEquals(1, $walkVertices->getNumberOfEdges());

        $this->assertGraphEquals($graphExpected, $walkVertices->createGraph());

        return $walk;
    }

    public function testWalkLoop()
    {
        // 1 -- 1
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $e1 = $v1->createEdge($v1);

        $walk = Walk::factoryFromEdges(array($e1), $v1);

        $this->assertEquals(2, count($walk->getVertices()));
        $this->assertEquals(1, $walk->getNumberOfEdges());
        $this->assertSame($v1, $walk->getVertexSource());
        $this->assertSame($v1, $walk->getVertexTarget());
        $this->assertTrue($walk->isValid());

        return $walk;
    }

    /**
     * @param Walk $walk
     * @depends testWalkLoop
     */
    public function testWalkInvalidByDestroyingEdge(Walk $walk)
    {
        // destroy first edge found
        foreach ($walk->getEdges() as $edge) {
            $edge->destroy();
            break;
        }

        $this->assertFalse($walk->isValid());
    }

    public function testWalkLoopCycle()
    {
        // 1 -- 1
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $e1 = $v1->createEdge($v1);

        $walk = Walk::factoryCycleFromEdges(array($e1), $v1);

        $this->assertEquals(2, count($walk->getVertices()));
        $this->assertEquals(1, $walk->getNumberOfEdges());
        $this->assertSame($v1, $walk->getVertexSource());
        $this->assertSame($v1, $walk->getVertexTarget());
        $this->assertTrue($walk->isValid());
    }

    public function testWalkCycleFromVerticesAutocomplete()
    {
        // 1 -- 2 -- 1
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $e1 = $v1->createEdge($v2);
        $e2 = $v2->createEdge($v1);

        // should actually be v1, v2, v1, but cycle factory automatically adds missing vertex + edge
        $walk = Walk::factoryCycleFromVertices(array($v1, $v2));

        $this->assertEquals(3, count($walk->getVertices()));
        $this->assertEquals(2, $walk->getNumberOfEdges());
        $this->assertSame($v1, $walk->getVertexSource());
        $this->assertSame($v1, $walk->getVertexTarget());
        $this->assertTrue($walk->isValid());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWalkCycleInvalid()
    {
        // 1 -- 2
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $e1 = $v1->createEdge($v2);

        Walk::factoryCycleFromEdges(array($e1), $v1);
    }
}

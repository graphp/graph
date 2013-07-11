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

        $this->assertEquals(3, $walk->getNumberOfVertices());
        $this->assertEquals(2, $walk->getNumberOfEdges());
        $this->assertSame($v1, $walk->getVertexSource());
        $this->assertSame($v3, $walk->getVertexTarget());
        $this->assertSame(array($v1, $e1, $v2, $e2, $v3), $walk->getAlternatingSequence());
        $this->assertTrue($walk->isValid());

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

    public function testWalkLoop()
    {
        // 1 -- 1
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $e1 = $v1->createEdge($v1);

        $walk = Walk::factoryFromEdges(array($e1), $v1);

        $this->assertEquals(2, $walk->getNumberOfVertices());
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

        $this->assertEquals(2, $walk->getNumberOfVertices());
        $this->assertEquals(1, $walk->getNumberOfEdges());
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

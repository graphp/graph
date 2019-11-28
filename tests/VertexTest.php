<?php

namespace Graphp\Graph\Tests;

use Graphp\Graph\Graph;
use Graphp\Graph\Tests\Attribute\AbstractAttributeAwareTest;
use Graphp\Graph\Vertex;

class VertexTest extends AbstractAttributeAwareTest
{
    private $graph;
    private $vertex;

    public function setUp()
    {
        $this->graph = new Graph();
        $this->vertex = $this->graph->createVertex();
    }

    public function testPrecondition()
    {
        $this->assertCount(1, $this->graph->getVertices());
        $this->assertEquals(array($this->vertex), $this->graph->getVertices()->getVector());
    }

    public function testConstructor()
    {
        $v2 = new Vertex($this->graph);

        $this->assertCount(2, $this->graph->getVertices());
        $this->assertEquals(array($this->vertex, $v2), $this->graph->getVertices()->getVector());
    }

    public function testEdges()
    {
        // v1 -> v2, v1 -- v3, v1 <- v4
        $v2 = $this->graph->createVertex();
        $v3 = $this->graph->createVertex();
        $v4 = $this->graph->createVertex();
        $e1 = $this->graph->createEdgeDirected($this->vertex, $v2);
        $e2 = $this->graph->createEdgeUndirected($this->vertex, $v3);
        $e3 = $this->graph->createEdgeDirected($v4, $this->vertex);

        $this->assertEquals(array($e1, $e2, $e3), $this->vertex->getEdges()->getVector());
        $this->assertEquals(array($e2, $e3), $this->vertex->getEdgesIn()->getVector());
        $this->assertEquals(array($e1, $e2), $this->vertex->getEdgesOut()->getVector());

        $this->assertTrue($this->vertex->hasEdgeTo($v2));
        $this->assertTrue($this->vertex->hasEdgeTo($v3));
        $this->assertFalse($this->vertex->hasEdgeTo($v4));

        $this->assertFalse($this->vertex->hasEdgeFrom($v2));
        $this->assertTrue($this->vertex->hasEdgeFrom($v3));
        $this->assertTrue($this->vertex->hasEdgeFrom($v4));

        $this->assertEquals(array($e1), $this->vertex->getEdgesTo($v2)->getVector());
        $this->assertEquals(array($e2), $this->vertex->getEdgesTo($v3)->getVector());
        $this->assertEquals(array(), $this->vertex->getEdgesTo($v4)->getVector());

        $this->assertEquals(array(), $this->vertex->getEdgesFrom($v2)->getVector());
        $this->assertEquals(array($e2), $this->vertex->getEdgesTo($v3)->getVector());
        $this->assertEquals(array($e3), $this->vertex->getEdgesFrom($v4)->getVector());

        $this->assertEquals(array($v2, $v3, $v4), $this->vertex->getVerticesEdge()->getVector());
        $this->assertEquals(array($v2, $v3), $this->vertex->getVerticesEdgeTo()->getVector());
        $this->assertEquals(array($v3, $v4), $this->vertex->getVerticesEdgeFrom()->getVector());
    }

    public function testUndirectedLoopEdgeReturnsEdgeTwiceInAndOut()
    {
        $edge = $this->graph->createEdgeUndirected($this->vertex, $this->vertex);

        $this->assertEquals(array($edge, $edge), $this->vertex->getEdges()->getVector());
        $this->assertEquals(array($edge, $edge), $this->vertex->getEdgesIn()->getVector());
        $this->assertEquals(array($edge, $edge), $this->vertex->getEdgesOut()->getVector());

        $this->assertEquals(array($this->vertex, $this->vertex), $this->vertex->getVerticesEdge()->getVector());
        $this->assertEquals(array($this->vertex, $this->vertex), $this->vertex->getVerticesEdgeTo()->getVector());
        $this->assertEquals(array($this->vertex, $this->vertex), $this->vertex->getVerticesEdgeFrom()->getVector());
    }

    public function testDirectedLoopEdgeReturnsEdgeTwiceUndirectedAndOnceEachInAndOut()
    {
        $edge = $this->graph->createEdgeDirected($this->vertex, $this->vertex);

        $this->assertEquals(array($edge, $edge), $this->vertex->getEdges()->getVector());
        $this->assertEquals(array($edge), $this->vertex->getEdgesIn()->getVector());
        $this->assertEquals(array($edge), $this->vertex->getEdgesOut()->getVector());

        $this->assertEquals(array($this->vertex, $this->vertex), $this->vertex->getVerticesEdge()->getVector());
        $this->assertEquals(array($this->vertex), $this->vertex->getVerticesEdgeTo()->getVector());
        $this->assertEquals(array($this->vertex), $this->vertex->getVerticesEdgeFrom()->getVector());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateEdgeOtherGraphFails()
    {
        $graphOther = new Graph();

        $this->graph->createEdgeUndirected($this->vertex, $graphOther->createVertex());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateEdgeDirectedOtherGraphFails()
    {
        $graphOther = new Graph();

        $this->graph->createEdgeDirected($this->vertex, $graphOther->createVertex());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRemoveInvalidEdge()
    {
        // 2 -- 3
        $v2 = $this->graph->createVertex();
        $v3 = $this->graph->createVertex();
        $edge = $this->graph->createEdgeUndirected($v2, $v3);

        $this->vertex->removeEdge($edge);
    }

    public function testRemoveWithEdgeLoopUndirected()
    {
        // 1 -- 1
        $this->graph->createEdgeUndirected($this->vertex, $this->vertex);

        $this->assertEquals(array($this->vertex), $this->graph->getVertices()->getVector());

        $this->vertex->destroy();

        $this->assertEquals(array(), $this->graph->getVertices()->getVector());
        $this->assertEquals(array(), $this->graph->getEdges()->getVector());
    }

    public function testRemoveWithEdgeLoopDirected()
    {
        // 1 --> 1
        $this->graph->createEdgeDirected($this->vertex, $this->vertex);

        $this->assertEquals(array($this->vertex), $this->graph->getVertices()->getVector());

        $this->vertex->destroy();

        $this->assertEquals(array(), $this->graph->getVertices()->getVector());
        $this->assertEquals(array(), $this->graph->getEdges()->getVector());
    }

    protected function createAttributeAware()
    {
        return new Vertex(new Graph());
    }
}

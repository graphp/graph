<?php

namespace Graphp\Graph\Tests;

use Graphp\Graph\Graph;
use Graphp\Graph\Vertex;

class VertexTest extends EntityTest
{
    private $graph;
    private $vertex;

    /**
     * @before
     */
    public function setUpVertex()
    {
        $this->graph = new Graph();
        $this->vertex = $this->graph->createVertex();
    }

    public function testPrecondition()
    {
        $this->assertCount(1, $this->graph->getVertices());
        $this->assertEquals(array($this->vertex), $this->graph->getVertices());
    }

    public function testConstructorWithoutAttributesHasNoAttributes()
    {
        $v2 = new Vertex($this->graph);

        $this->assertCount(2, $this->graph->getVertices());
        $this->assertEquals(array($this->vertex, $v2), $this->graph->getVertices());

        $this->assertNull($v2->getAttribute('hello'));
        $this->assertEquals('default', $v2->getAttribute('hello', 'default'));
        $this->assertEquals(array(), $v2->getAttributes());
    }

    public function testConstructorWithAttributesReturnsAttributes()
    {
        $v2 = new Vertex($this->graph, array('hello' => 'wörld'));

        $this->assertEquals('wörld', $v2->getAttribute('hello'));
        $this->assertEquals(array('hello' => 'wörld'), $v2->getAttributes());
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

        $this->assertEquals(array($e1, $e2, $e3), $this->vertex->getEdges());
        $this->assertEquals(array($e2, $e3), array_values($this->vertex->getEdgesIn()));
        $this->assertEquals(array($e1, $e2), array_values($this->vertex->getEdgesOut()));

        $this->assertTrue($this->vertex->hasEdgeTo($v2));
        $this->assertTrue($this->vertex->hasEdgeTo($v3));
        $this->assertFalse($this->vertex->hasEdgeTo($v4));

        $this->assertFalse($this->vertex->hasEdgeFrom($v2));
        $this->assertTrue($this->vertex->hasEdgeFrom($v3));
        $this->assertTrue($this->vertex->hasEdgeFrom($v4));

        $this->assertEquals(array($e1), $this->vertex->getEdgesTo($v2));
        $this->assertEquals(array($e2), array_values($this->vertex->getEdgesTo($v3)));
        $this->assertEquals(array(), $this->vertex->getEdgesTo($v4));

        $this->assertEquals(array(), $this->vertex->getEdgesFrom($v2));
        $this->assertEquals(array($e2), array_values($this->vertex->getEdgesTo($v3)));
        $this->assertEquals(array($e3), $this->vertex->getEdgesFrom($v4));

        $this->assertEquals(array($v2, $v3, $v4), $this->vertex->getVerticesEdge());
        $this->assertEquals(array($v2, $v3), $this->vertex->getVerticesEdgeTo());
        $this->assertEquals(array($v3, $v4), $this->vertex->getVerticesEdgeFrom());
    }

    public function testUndirectedLoopEdgeReturnsEdgeTwiceInAndOut()
    {
        $edge = $this->graph->createEdgeUndirected($this->vertex, $this->vertex);

        $this->assertEquals(array($edge, $edge), $this->vertex->getEdges());
        $this->assertEquals(array($edge, $edge), $this->vertex->getEdgesIn());
        $this->assertEquals(array($edge, $edge), $this->vertex->getEdgesOut());

        $this->assertEquals(array($this->vertex, $this->vertex), $this->vertex->getVerticesEdge());
        $this->assertEquals(array($this->vertex, $this->vertex), $this->vertex->getVerticesEdgeTo());
        $this->assertEquals(array($this->vertex, $this->vertex), $this->vertex->getVerticesEdgeFrom());
    }

    public function testDirectedLoopEdgeReturnsEdgeTwiceUndirectedAndOnceEachInAndOut()
    {
        $edge = $this->graph->createEdgeDirected($this->vertex, $this->vertex);

        $this->assertEquals(array($edge, $edge), $this->vertex->getEdges());
        $this->assertEquals(array($edge), $this->vertex->getEdgesIn());
        $this->assertEquals(array($edge), $this->vertex->getEdgesOut());

        $this->assertEquals(array($this->vertex, $this->vertex), $this->vertex->getVerticesEdge());
        $this->assertEquals(array($this->vertex), $this->vertex->getVerticesEdgeTo());
        $this->assertEquals(array($this->vertex), $this->vertex->getVerticesEdgeFrom());
    }

    public function testCreateEdgeOtherGraphFails()
    {
        $graphOther = new Graph();

        $this->setExpectedException('InvalidArgumentException');
        $this->graph->createEdgeUndirected($this->vertex, $graphOther->createVertex());
    }

    public function testCreateEdgeDirectedOtherGraphFails()
    {
        $graphOther = new Graph();

        $this->setExpectedException('InvalidArgumentException');
        $this->graph->createEdgeDirected($this->vertex, $graphOther->createVertex());
    }


    protected function createEntity()
    {
        return new Vertex(new Graph());
    }
}

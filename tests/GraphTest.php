<?php

namespace Graphp\Graph\Tests;

use Graphp\Graph\Graph;
use Graphp\Graph\Tests\Attribute\AbstractAttributeAwareTest;

class GraphTest extends AbstractAttributeAwareTest
{
    public function testCanCreateVertex()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex();
        $this->assertInstanceOf('\Graphp\Graph\Vertex', $vertex);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateEdgeUndirectedWithVerticesFromOtherGraphThrows()
    {
        // 1, 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();

        $graph2 = new Graph();
        $graph2->createEdgeUndirected($v1, $v2);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateEdgeDirectedWithVerticesFromOtherGraphThrows()
    {
        // 1, 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();

        $graph2 = new Graph();
        $graph2->createEdgeDirected($v1, $v2);
    }

    public function testCreateMultigraph()
    {
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $graph->createEdgeUndirected($v1, $v2);
        $graph->createEdgeUndirected($v1, $v2);

        $this->assertEquals(2, count($graph->getEdges()));
        $this->assertEquals(2, count($v1->getEdges()));

        $this->assertEquals(array($v2, $v2), $v1->getVerticesEdge()->getVector());
    }

    public function testCreateMixedGraph()
    {
        // v1 -- v2 -> v3
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $v3 = $graph->createVertex();

        $graph->createEdgeUndirected($v1, $v2);
        $graph->createEdgeDirected($v2, $v3);

        $this->assertEquals(2, count($graph->getEdges()));

        $this->assertEquals(2, count($v2->getEdges()));
        $this->assertEquals(2, count($v2->getEdgesOut()));
        $this->assertEquals(1, count($v2->getEdgesIn()));

        $this->assertEquals(array($v1, $v3), $v2->getVerticesEdgeTo()->getVector());
        $this->assertEquals(array($v1), $v2->getVerticesEdgeFrom()->getVector());
    }

    public function testRemoveEdge()
    {
        // 1 -- 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $edge = $graph->createEdgeUndirected($v1, $v2);

        $this->assertEquals(array($edge), $graph->getEdges()->getVector());

        $edge->destroy();
        //$graph->removeEdge($edge);

        $this->assertEquals(array(), $graph->getEdges()->getVector());

        return $graph;
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRemoveEdgeInvalid()
    {
        // 1 -- 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $edge = $graph->createEdgeUndirected($v1, $v2);

        $edge->destroy();
        $edge->destroy();
    }

    public function testRemoveVertex()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex();

        $this->assertEquals(array($vertex), $graph->getVertices()->getVector());

        $vertex->destroy();

        $this->assertEquals(array(), $graph->getVertices()->getVector());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRemoveVertexInvalid()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex();

        $vertex->destroy();
        $vertex->destroy();
    }

    public function testGraphCloneEmptyGraph()
    {
        $graph = new Graph();

        $newgraph = clone $graph;

        $this->assertCount(0, $newgraph->getVertices());
        $this->assertCount(0, $newgraph->getEdges());
        $this->assertGraphEquals($graph, $newgraph);
        $this->assertNotSame($graph, $newgraph);
    }

    public function testGraphCloneMixedEdges()
    {
        // 1 -> 2 -- 3
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $v3 = $graph->createVertex();
        $graph->createEdgeDirected($v1, $v2);
        $graph->createEdgeUndirected($v2, $v3);

        $newgraph = clone $graph;

        $this->assertCount(3, $newgraph->getVertices());
        $this->assertCount(2, $newgraph->getEdges());
        $this->assertGraphEquals($graph, $newgraph);
    }

    public function testGraphCloneParallelEdges()
    {
        // 1 -> 2
        // |    ^
        // \----/
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $graph->createEdgeDirected($v1, $v2);
        $graph->createEdgeDirected($v1, $v2);

        $newgraph = clone $graph;

        $this->assertCount(2, $newgraph->getVertices());
        $this->assertCount(2, $newgraph->getEdges());
        $this->assertGraphEquals($graph, $newgraph);
    }

    public function testGraphCloneLoopGraphWithAttributes()
    {
        // 1 -\
        // ^  |
        // \--/
        $graph = new Graph();
        $graph->setAttribute('color', 'grey');
        $v = $graph->createVertex()->setAttribute('color', 'blue');
        $graph->createEdgeDirected($v, $v)->setAttribute('color', 'red');

        $newgraph = clone $graph;

        $this->assertCount(1, $newgraph->getVertices());
        $this->assertCount(1, $newgraph->getEdges());
        $this->assertGraphEquals($graph, $newgraph);

        $graphClonedTwice = clone $newgraph;

        $this->assertGraphEquals($graph, $graphClonedTwice);

        $this->assertNotSame($graph->getEdges(), $newgraph->getEdges());
        $this->assertNotSame($graph->getVertices(), $newgraph->getVertices());
    }

    protected function createAttributeAware()
    {
        return new Graph();
    }
}

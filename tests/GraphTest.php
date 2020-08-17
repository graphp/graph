<?php

namespace Graphp\Graph\Tests;

use Graphp\Graph\Graph;
use Graphp\Graph\Set\Edges;
use Graphp\Graph\Set\Vertices;

class GraphTest extends EntityTest
{
    public function testEmptyGraphHasNoAttributes()
    {
        $graph = new Graph();
        $this->assertNull($graph->getAttribute('hello'));
        $this->assertEquals('default', $graph->getAttribute('hello', 'default'));
        $this->assertEquals(array(), $graph->getAttributes());
    }

    public function testEmptyGraphWithAttributeReturnsAttributes()
    {
        $graph = new Graph(array('hello' => 'wörld'));
        $this->assertEquals('wörld', $graph->getAttribute('hello'));
        $this->assertEquals(array('hello' => 'wörld'), $graph->getAttributes());
    }

    public function testCanCreateVertex()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex();
        $this->assertInstanceOf('\Graphp\Graph\Vertex', $vertex);
    }

    public function testCreateEdgeUndirectedWithVerticesFromOtherGraphThrows()
    {
        // 1, 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();

        $graph2 = new Graph();

        $this->setExpectedException('InvalidArgumentException');
        $graph2->createEdgeUndirected($v1, $v2);
    }

    public function testCreateEdgeDirectedWithVerticesFromOtherGraphThrows()
    {
        // 1, 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();

        $graph2 = new Graph();

        $this->setExpectedException('InvalidArgumentException');
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

    public function testWithoutEdgeReturnsNewGraphAndDoesNotModifyOriginal()
    {
        // 1 -- 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $edge = $graph->createEdgeUndirected($v1, $v2);

        $this->assertEquals(array($edge), $graph->getEdges()->getVector());

        $new = $graph->withoutEdge($edge);

        $this->assertInstanceOf(get_class($graph), $new);
        $this->assertEquals(array(), $new->getEdges()->getVector());
        $this->assertEquals(array($edge), $graph->getEdges()->getVector());
    }

    public function testWithoutEdgesSameTwiceReturnsNewGraphAndDoesNotModifyOriginal()
    {
        // 1 -- 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $edge = $graph->createEdgeUndirected($v1, $v2);

        $this->assertEquals(array($edge), $graph->getEdges()->getVector());

        $new = $graph->withoutEdges(new Edges(array($edge, $edge)));

        $this->assertInstanceOf(get_class($graph), $new);
        $this->assertEquals(array(), $new->getEdges()->getVector());
        $this->assertEquals(array($edge), $graph->getEdges()->getVector());
    }

    public function testWithoutEdgeFromOtherGraphReturnsSameGraphWithoutModification()
    {
        // 1 -- 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $graph->createEdgeUndirected($v1, $v2);

        $clone = clone $graph;
        $edge = $clone->getEdges()->getEdgeFirst();

        $new = $graph->withoutEdge($edge);

        $this->assertSame($new, $graph);
        $this->assertEquals(array($edge), $graph->getEdges()->getVector());
    }

    public function testWithoutVertexReturnsNewGraphAndDoesNotModifyOriginal()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex();

        $this->assertEquals(array($vertex), $graph->getVertices()->getVector());

        $new = $graph->withoutVertex($vertex);

        $this->assertInstanceOf(get_class($graph), $new);
        $this->assertEquals(array(), $new->getVertices()->getVector());
        $this->assertEquals(array($vertex), $graph->getVertices()->getVector());
    }

    public function testWithoutVerticesTwiceReturnsNewGraphAndDoesNotModifyOriginal()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex();

        $this->assertEquals(array($vertex), $graph->getVertices()->getVector());

        $new = $graph->withoutVertices(new Vertices(array($vertex, $vertex)));

        $this->assertInstanceOf(get_class($graph), $new);
        $this->assertEquals(array(), $new->getVertices()->getVector());
        $this->assertEquals(array($vertex), $graph->getVertices()->getVector());
    }

    public function testWithoutVertexFromOtherGraphReturnsSameGraphWithoutModification()
    {
        $graph = new Graph();
        $graph->createVertex();

        $other = new Graph();
        $vertex = $other->createVertex();

        $new = $graph->withoutVertex($vertex);

        $this->assertSame($new, $graph);
        $this->assertEquals(array($vertex), $graph->getVertices()->getVector());
    }

    public function testWithoutVertexRemovesAttachedUndirectedEdge()
    {
        // 1 -- 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $graph->createEdgeUndirected($v1, $v2);

        $new = $graph->withoutVertex($v1);

        $this->assertCount(1, $new->getVertices());
        $this->assertCount(0, $new->getEdges());
    }

    public function testWithoutVertexWithUndirectedLoopReturnsEmptyGraph()
    {
        // 1 -\
        // |  |
        // \--/
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $graph->createEdgeUndirected($v1, $v1);

        $new = $graph->withoutVertex($v1);

        $this->assertCount(0, $new->getVertices());
        $this->assertCount(0, $new->getEdges());
    }

    public function testWithoutVertexWithUndirectedLoopReturnsRemainingGraph()
    {
        // 1 -- 2 -\
        // ^    |  |
        // |    \--/
        // 3
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $v3 = $graph->createVertex();
        $graph->createEdgeDirected($v3, $v1);
        $graph->createEdgeUndirected($v2, $v2);

        $new = $graph->withoutVertex($v2);

        $this->assertCount(2, $new->getVertices());
        $this->assertCount(1, $new->getEdges());
    }

    public function testWithoutVertexWithDirectedLoopReturnsEmptyGraph()
    {
        // 1 -\
        // ^  |
        // \--/
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $graph->createEdgeDirected($v1, $v1);

        $new = $graph->withoutVertex($v1);

        $this->assertCount(0, $new->getVertices());
        $this->assertCount(0, $new->getEdges());
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
        $graph = new Graph(array('color' => 'grey'));
        $v = $graph->createVertex(array('color' => 'blue'));
        $graph->createEdgeDirected($v, $v, array('color' => 'red'));

        $newgraph = clone $graph;

        $this->assertCount(1, $newgraph->getVertices());
        $this->assertCount(1, $newgraph->getEdges());
        $this->assertGraphEquals($graph, $newgraph);

        $graphClonedTwice = clone $newgraph;

        $this->assertGraphEquals($graph, $graphClonedTwice);

        $this->assertNotSame($graph->getEdges(), $newgraph->getEdges());
        $this->assertNotSame($graph->getVertices(), $newgraph->getVertices());
    }

    protected function createEntity()
    {
        return new Graph();
    }
}

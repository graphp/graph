<?php

namespace Graphp\Graph\Tests;

use Graphp\Graph\Edge;
use Graphp\Graph\Graph;
use Graphp\Graph\Set\Edges;
use Graphp\Graph\Set\Vertices;
use Graphp\Graph\Walk;

class WalkTest extends TestCase
{
    /**
     * @expectedException UnderflowException
     */
    public function testWalkCanNotBeEmpty()
    {
        Walk::factoryCycleFromVertices(new Vertices(array()));
    }

    public function testWalkPath()
    {
        // 1 -- 2 -- 3
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $v3 = $graph->createVertex();
        $e1 = $graph->createEdgeDirected($v1, $v2);
        $e2 = $graph->createEdgeDirected($v2, $v3);

        $walk = Walk::factoryFromEdges(new Edges(array($e1, $e2)), $v1);

        $this->assertSame($graph, $walk->getGraph());
        $this->assertEquals(3, count($walk->getVertices()));
        $this->assertEquals(2, count($walk->getEdges()));
        $this->assertSame($v1, $walk->getVertices()->getVertexFirst());
        $this->assertSame($v3, $walk->getVertices()->getVertexLast());
        $this->assertSame(array($v1, $e1, $v2, $e2, $v3), $walk->getAlternatingSequence());

        return $walk;
    }

    public function testWalkWithinGraph()
    {
        // 1 -- 2 -- 3
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $v3 = $graph->createVertex();
        $e1 = $graph->createEdgeDirected($v1, $v2);
        $graph->createEdgeDirected($v2, $v3);

        // construct partial walk "1 -- 2"
        $walk = Walk::factoryFromEdges(new Edges(array($e1)), $v1);

        $this->assertSame($graph, $walk->getGraph());
        $this->assertEquals(2, count($walk->getVertices()));
        $this->assertEquals(1, count($walk->getEdges()));
        $this->assertSame($v1, $walk->getVertices()->getVertexFirst());
        $this->assertSame($v2, $walk->getVertices()->getVertexLast());
        $this->assertSame(array($v1, $e1, $v2), $walk->getAlternatingSequence());

        // construct same partial walk "1 -- 2"
        $walkVertices = Walk::factoryFromVertices(new Vertices(array($v1, $v2)));

        $this->assertEquals(2, count($walkVertices->getVertices()));
        $this->assertEquals(1, count($walkVertices->getEdges()));

        return $walk;
    }

    public function testWalkLoop()
    {
        // 1 -- 1
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $e1 = $graph->createEdgeUndirected($v1, $v1);

        $walk = Walk::factoryFromEdges(new Edges(array($e1)), $v1);

        $this->assertEquals(2, count($walk->getVertices()));
        $this->assertEquals(1, count($walk->getEdges()));
        $this->assertSame($v1, $walk->getVertices()->getVertexFirst());
        $this->assertSame($v1, $walk->getVertices()->getVertexLast());

        return $walk;
    }

    public function testWalkLoopCycle()
    {
        // 1 -- 1
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $e1 = $graph->createEdgeUndirected($v1, $v1);

        $walk = Walk::factoryCycleFromEdges(new Edges(array($e1)), $v1);

        $this->assertEquals(2, count($walk->getVertices()));
        $this->assertEquals(1, count($walk->getEdges()));
        $this->assertSame($v1, $walk->getVertices()->getVertexFirst());
        $this->assertSame($v1, $walk->getVertices()->getVertexLast());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWalkCycleFromVerticesIncomplete()
    {
        // 1 -- 2 -- 1
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $graph->createEdgeUndirected($v1, $v2);
        $graph->createEdgeUndirected($v2, $v1);

        // should actually be [v1, v2, v1]
        Walk::factoryCycleFromVertices(new Vertices(array($v1, $v2)));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWalkCycleInvalid()
    {
        // 1 -- 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $e1 = $graph->createEdgeUndirected($v1, $v2);

        Walk::factoryCycleFromEdges(new Edges(array($e1)), $v1);
    }

    public function testFactoryCycleFromEdgesWithLoopCycle()
    {
        // 1 --\
        // ^   |
        // \---/
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $e1 = $graph->createEdgeDirected($v1, $v1);

        $cycle = Walk::factoryCycleFromEdges(new Edges(array($e1)), $v1);

        $this->assertCount(2, $cycle->getVertices());
        $this->assertCount(1, $cycle->getEdges());
        $this->assertSame($v1, $cycle->getVertices()->getVertexFirst());
        $this->assertSame($v1, $cycle->getVertices()->getVertexLast());
    }

    public function testFactoryCycleFromVerticesWithLoopCycle()
    {
        // 1 --\
        // ^   |
        // \---/
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $graph->createEdgeDirected($v1, $v1);

        $cycle = Walk::factoryCycleFromVertices(new Vertices(array($v1, $v1)));

        $this->assertCount(2, $cycle->getVertices());
        $this->assertCount(1, $cycle->getEdges());
        $this->assertSame($v1, $cycle->getVertices()->getVertexFirst());
        $this->assertSame($v1, $cycle->getVertices()->getVertexLast());
    }


    /**
     * @expectedException InvalidArgumentException
     */
    public function testFactoryCycleFromVerticesThrowsWhenCycleIsIncomplete()
    {
        $graph = new Graph();
        $v1 = $graph->createVertex();

        // should actually be [v1, v1]
        Walk::factoryCycleFromVertices(new Vertices(array($v1)));
    }

    public function testFactoryFromVertices()
    {
        // 1 -- 2
        // |    |
        // \----/
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $e1 = $graph->createEdgeUndirected($v1, $v2)->setAttribute('weight', 10);
        $e2 = $graph->createEdgeUndirected($v1, $v2)->setAttribute('weight', 20);

        // any edge in walk
        $walk = Walk::factoryFromVertices(new Vertices(array($v1, $v2)));

        // edge with weight 10
        $walk = Walk::factoryFromVertices(new Vertices(array($v1, $v2)), function (Edge $edge) {
            return $edge->getAttribute('weight');
        });
        $this->assertSame($e1, $walk->getEdges()->getEdgeFirst());

        // edge with weight 10
        $walk = Walk::factoryFromVertices(new Vertices(array($v1, $v2)), 'weight');
        $this->assertSame($e1, $walk->getEdges()->getEdgeFirst());

        // edge with weight 20
        $walk = Walk::factoryFromVertices(new Vertices(array($v1, $v2)), function (Edge $edge) {
            return $edge->getAttribute('weight');
        }, true);
        $this->assertSame($e2, $walk->getEdges()->getEdgeFirst());

        // edge with weight 20
        $walk = Walk::factoryFromVertices(new Vertices(array($v1, $v2)), 'weight', true);
        $this->assertSame($e2, $walk->getEdges()->getEdgeFirst());
    }
}

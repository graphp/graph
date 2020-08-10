<?php

namespace Graphp\Graph\Tests;

use Graphp\Graph\Edge;
use Graphp\Graph\Graph;
use Graphp\Graph\Walk;

class WalkTest extends TestCase
{
    public function testWalkCanNotBeEmpty()
    {
        $this->setExpectedException('UnderflowException');
        Walk::factoryCycleFromVertices(array());
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

        $walk = Walk::factoryFromEdges(array($e1, $e2), $v1);

        $this->assertSame($graph, $walk->getGraph());
        $this->assertEquals(3, count($walk->getVertices()));
        $this->assertEquals(2, count($walk->getEdges()));

        $vertices = $walk->getVertices();
        $this->assertSame($v1, reset($vertices));
        $this->assertSame($v3, end($vertices));
        $this->assertSame(array($v1, $e1, $v2, $e2, $v3), $walk->getAlternatingSequence());
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
        $walk = Walk::factoryFromEdges(array($e1), $v1);

        $this->assertSame($graph, $walk->getGraph());
        $this->assertEquals(2, count($walk->getVertices()));
        $this->assertEquals(1, count($walk->getEdges()));

        $vertices = $walk->getVertices();
        $this->assertSame($v1, reset($vertices));
        $this->assertSame($v2, end($vertices));
        $this->assertSame(array($v1, $e1, $v2), $walk->getAlternatingSequence());

        // construct same partial walk "1 -- 2"
        $walkVertices = Walk::factoryFromVertices(array($v1, $v2));

        $this->assertEquals(2, count($walkVertices->getVertices()));
        $this->assertEquals(1, count($walkVertices->getEdges()));
    }

    public function testFactoryFromEdgesWithTrivialGraphHasOneVertexAndNoEdges()
    {
        // 1
        $graph = new Graph();
        $v1 = $graph->createVertex();

        $walk = Walk::factoryFromEdges(array(), $v1);

        $this->assertEquals(array($v1), $walk->getVertices());
        $this->assertEquals(array(), $walk->getEdges());
    }

    public function testFactoryFromEdgesWithArrayKeysWillBeIgnoredForGetEdges()
    {
        // 1 -- 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $e1 = $graph->createEdgeDirected($v1, $v2);

        $walk = Walk::factoryFromEdges(array('first' => $e1), $v1);

        $this->assertEquals(array($v1, $v2), $walk->getVertices());
        $this->assertEquals(array($e1), $walk->getEdges());
    }

    public function testFactoryFromVerticesWithTrivialGraphHasOneVertexAndNoEdges()
    {
        // 1
        $graph = new Graph();
        $v1 = $graph->createVertex();

        $walk = Walk::factoryFromVertices(array($v1));

        $this->assertEquals(array($v1), $walk->getVertices());
        $this->assertEquals(array(), $walk->getEdges());
    }

    public function testFactoryFromVerticesWithArrayKeysWillBeIgnoredForGetVertices()
    {
        // 1 -- 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $e1 = $graph->createEdgeDirected($v1, $v2);

        $walk = Walk::factoryFromVertices(array('first' => $v1, 'second' => $v2));

        $this->assertEquals(array($v1, $v2), $walk->getVertices());
        $this->assertEquals(array($e1), $walk->getEdges());
    }

    public function testFactoryFromVerticesWithUnconnectedComponentsThrows()
    {
        // 1, 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();

        $this->setExpectedException('UnderflowException');
        Walk::factoryFromVertices(array($v1, $v2));
    }

    public function testWalkLoop()
    {
        // 1 -- 1
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $e1 = $graph->createEdgeUndirected($v1, $v1);

        $walk = Walk::factoryFromEdges(array($e1), $v1);

        $this->assertEquals(2, count($walk->getVertices()));
        $this->assertEquals(1, count($walk->getEdges()));

        $vertices = $walk->getVertices();
        $this->assertSame($v1, reset($vertices));
        $this->assertSame($v1, end($vertices));
    }

    public function testWalkLoopCycle()
    {
        // 1 -- 1
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $e1 = $graph->createEdgeUndirected($v1, $v1);

        $walk = Walk::factoryCycleFromEdges(array($e1), $v1);

        $this->assertEquals(2, count($walk->getVertices()));
        $this->assertEquals(1, count($walk->getEdges()));

        $vertices = $walk->getVertices();
        $this->assertSame($v1, reset($vertices));
        $this->assertSame($v1, end($vertices));
    }

    public function testWalkCycleFromVerticesIncomplete()
    {
        // 1 -- 2 -- 1
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $graph->createEdgeUndirected($v1, $v2);
        $graph->createEdgeUndirected($v2, $v1);

        // should actually be [v1, v2, v1]
        $this->setExpectedException('InvalidArgumentException');
        Walk::factoryCycleFromVertices(array($v1, $v2));
    }

    public function testWalkCycleInvalid()
    {
        // 1 -- 2
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $e1 = $graph->createEdgeUndirected($v1, $v2);

        $this->setExpectedException('InvalidArgumentException');
        Walk::factoryCycleFromEdges(array($e1), $v1);
    }

    public function testFactoryCycleFromEdgesWithLoopCycle()
    {
        // 1 --\
        // ^   |
        // \---/
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $e1 = $graph->createEdgeDirected($v1, $v1);

        $cycle = Walk::factoryCycleFromEdges(array($e1), $v1);

        $this->assertCount(2, $cycle->getVertices());
        $this->assertCount(1, $cycle->getEdges());

        $vertices = $cycle->getVertices();
        $this->assertSame($v1, reset($vertices));
        $this->assertSame($v1, end($vertices));
    }

    public function testFactoryCycleFromVerticesWithLoopCycle()
    {
        // 1 --\
        // ^   |
        // \---/
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $graph->createEdgeDirected($v1, $v1);

        $cycle = Walk::factoryCycleFromVertices(array($v1, $v1));

        $this->assertCount(2, $cycle->getVertices());
        $this->assertCount(1, $cycle->getEdges());

        $vertices = $cycle->getVertices();
        $this->assertSame($v1, reset($vertices));
        $this->assertSame($v1, end($vertices));
    }

    public function testFactoryCycleFromVerticesThrowsWhenCycleIsIncomplete()
    {
        $graph = new Graph();
        $v1 = $graph->createVertex();

        // should actually be [v1, v1]
        $this->setExpectedException('InvalidArgumentException');
        Walk::factoryCycleFromVertices(array($v1));
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
        $walk = Walk::factoryFromVertices(array($v1, $v2));

        // edge with weight 10
        $walk = Walk::factoryFromVertices(array($v1, $v2), function (Edge $edge) {
            return $edge->getAttribute('weight');
        });
        $edges = $walk->getEdges();
        $this->assertSame($e1, reset($edges));

        // edge with weight 10
        $walk = Walk::factoryFromVertices(array($v1, $v2), 'weight');
        $edges = $walk->getEdges();
        $this->assertSame($e1, reset($edges));

        // edge with weight 20
        $walk = Walk::factoryFromVertices(array($v1, $v2), function (Edge $edge) {
            return $edge->getAttribute('weight');
        }, true);
        $edges = $walk->getEdges();
        $this->assertSame($e2, reset($edges));

        // edge with weight 20
        $walk = Walk::factoryFromVertices(array($v1, $v2), 'weight', true);
        $edges = $walk->getEdges();
        $this->assertSame($e2, reset($edges));
    }
}

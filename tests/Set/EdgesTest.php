<?php

namespace Graphp\Graph\Tests\Set;

use Graphp\Graph\Edge;
use Graphp\Graph\Graph;
use Graphp\Graph\Set\Edges;
use Graphp\Graph\Tests\TestCase;

class EdgesTest extends TestCase
{
    /**
     * @param array $edges
     * @return Edges
     */
    protected function createEdges(array $edges)
    {
        return new Edges($edges);
    }

    public function testEmpty()
    {
        $edges = $this->createEdges(array());

        $this->assertEquals(0, $edges->count());
        $this->assertEquals(0, count($edges));
        $this->assertEquals(array(), $edges->getVector());
        $this->assertTrue($edges->isEmpty());
        $this->assertTrue($edges->getEdges()->isEmpty());
        $this->assertTrue($edges->getEdgesOrder(function (Edge $edge) {
            return $edge->getAttribute('weight');
        })->isEmpty());
        $this->assertTrue($edges->getEdgesOrder('weight')->isEmpty());
        $this->assertTrue($edges->getEdgesDistinct()->isEmpty());
        $this->assertTrue($edges->getEdgesMatch(function() { })->isEmpty());

        return $edges;
    }

    /**
     * @param Edges $edges
     * @depends testEmpty
     */
    public function testEmptyDoesNotHaveFirst(Edges $edges)
    {
        $this->setExpectedException('UnderflowException');
        $edges->getEdgeFirst();
    }

    /**
     * @param Edges $edges
     * @depends testEmpty
     */
    public function testEmptyDoesNotHaveLast(Edges $edges)
    {
        $this->setExpectedException('UnderflowException');
        $edges->getEdgeLast();
    }

    /**
     * @param Edges $edges
     * @depends testEmpty
     */
    public function testEmptyDoesNotHaveRandom(Edges $edges)
    {
        $this->setExpectedException('UnderflowException');
        $edges->getEdgeRandom();
    }

    /**
     * @param Edges $edges
     * @depends testEmpty
     */
    public function testEmptyDoesNotHaveOrdered(Edges $edges)
    {
        $this->setExpectedException('UnderflowException');
        $edges->getEdgeOrder('weight');
    }

    public function testTwo()
    {
        // 1 -- 2 -- 3
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $v3 = $graph->createVertex();
        $e1 = $graph->createEdgeUndirected($v1, $v2);
        $e2 = $graph->createEdgeUndirected($v2, $v3);

        $edges = $this->createEdges(array($e1, $e2));

        $this->assertEquals(2, count($edges));

        $this->assertSame($e1, $edges->getEdgeFirst());
        $this->assertSame($e1, $edges->getEdgeIndex(0));

        $this->assertSame($e2, $edges->getEdgeLast());
        $this->assertSame($e2, $edges->getEdgeIndex(1));

        $this->assertEquals(0, $edges->getIndexEdge($e1));

        return $edges;
    }

    /**
     * @param Edges $edges
     * @depends testTwo
     */
    public function testTwoDoesNotContainIndex3(Edges $edges)
    {
        $this->setExpectedException('OutOfBoundsException');
        $edges->getEdgeIndex(3);
    }

    /**
     * @param Edges $edges
     * @depends testTwo
     */
    public function testTwoDoesNotContainEdge3(Edges $edges)
    {
        $graph = new Graph();
        $v3 = $graph->createVertex();
        $e3 = $graph->createEdgeUndirected($v3, $v3);

        $this->setExpectedException('OutOfBoundsException');
        $edges->getIndexEdge($e3);
    }

    /**
     * @param Edges $edges
     * @depends testTwo
     */
    public function testTwoAsMap(Edges $edges)
    {
        $distinct = $edges->getEdgesDistinct();

        $this->assertInstanceOf('Graphp\Graph\Set\Edges', $distinct);
        $this->assertEquals(2, count($distinct));
    }

    /**
     * @param Edges $edges
     * @depends testTwo
     */
    public function testTwoRandom(Edges $edges)
    {
        $edgeRandom = $edges->getEdgeRandom();

        $this->assertInstanceOf('Graphp\Graph\Edge', $edgeRandom);
        $edges->getEdgeIndex($edges->getIndexEdge($edgeRandom));
    }

    /**
     * @param Edges $edges
     * @depends testTwo
     */
    public function testTwoShuffled(Edges $edges)
    {
        $edgesRandom = $edges->getEdgesShuffled();

        $this->assertInstanceOf('Graphp\Graph\Set\Edges', $edgesRandom);
        $this->assertEquals(2, count($edgesRandom));
    }

    /**
     * @param Edges $edges
     * @depends testTwo
     */
    public function testTwoIterator(Edges $edges)
    {
        $this->assertInstanceOf('Iterator', $edges->getIterator());

        $values = array_values(iterator_to_array($edges));
        $this->assertEquals($edges->getVector(), $values);
    }

    /**
     * @param Edges $edges
     * @depends testTwo
     */
    public function testTwoMatch(Edges $edges)
    {
        $edgesMatch = $edges->getEdgesMatch(array($this, 'returnTrue'));
        $this->assertEquals($edges->getVector(), $edgesMatch->getVector());

        $edgeMatch = $edges->getEdgeMatch(array($this, 'returnTrue'));
        $this->assertEquals($edges->getEdgeFirst(), $edgeMatch);
    }

    /**
     * @param Edges $edges
     * @depends testTwo
     */
    public function testTwoMatchEmpty(Edges $edges)
    {
        $edgesMatch = $edges->getEdgesMatch(array($this, 'returnFalse'));
        $this->assertCount(0, $edgesMatch);
    }

    /**
     * @param Edges $edges
     * @depends testTwo
     */
    public function testTwoMatchFail(Edges $edges)
    {
        $this->setExpectedException('UnderflowException');
        $edges->getEdgeMatch(array($this, 'returnFalse'));
    }

    public function returnTrue(Edge $edge)
    {
        return true;
    }

    public function returnFalse(Edge $edge)
    {
        return false;
    }

    public function testOrderByGroup()
    {
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $graph->createEdgeUndirected($v1, $v2)->setAttribute('weight', 1);
        $graph->createEdgeUndirected($v1, $v2)->setAttribute('weight', 100);
        $graph->createEdgeUndirected($v1, $v2)->setAttribute('weight', 5);
        $graph->createEdgeUndirected($v1, $v2)->setAttribute('weight', 100);
        $graph->createEdgeUndirected($v1, $v2)->setAttribute('weight', 100);
        $graph->createEdgeUndirected($v1, $v2)->setAttribute('weight', 2);
        $biggest = $graph->createEdgeUndirected($v1, $v2)->setAttribute('weight', 200);

        $edges = $graph->getEdges();
        $edgesOrdered = $edges->getEdgesOrder(function (Edge $edge) {
            return $edge->getAttribute('weight');
        });

        $this->assertInstanceOf('Graphp\Graph\Set\Edges', $edgesOrdered);
        $this->assertEquals(1, $edgesOrdered->getEdgeFirst()->getAttribute('weight'));
        $this->assertEquals(200, $edgesOrdered->getEdgeLast()->getAttribute('weight'));

        $this->assertSame($biggest, $edgesOrdered->getEdgeLast());
        $this->assertSame($biggest, $edges->getEdgeOrder(function (Edge $edge) {
            return $edge->getAttribute('weight');
        }, true));

        $sumweights = function(Edge $edge) {
            return $edge->getAttribute('weight');
        };
        $this->assertSame(508, $edges->getSumCallback($sumweights));
        $this->assertSame(508, $edgesOrdered->getSumCallback($sumweights));
    }

    public function testOrderByAttribute()
    {
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $e1 = $graph->createEdgeUndirected($v1, $v2)->setAttribute('weight', 20);
        $e2 = $graph->createEdgeUndirected($v1, $v2)->setAttribute('weight', 10);

        $edges = $graph->getEdges()->getEdgesOrder('weight');

        $this->assertInstanceOf('Graphp\Graph\Set\Edges', $edges);
        $this->assertSame($e2, $edges->getEdgeFirst());
        $this->assertSame($e1, $edges->getEdgeLast());

        $this->assertSame($e1, $edges->getEdgeOrder('weight', true));

        $this->assertSame(30, $graph->getEdges()->getSumCallback('weight'));
        $this->assertSame(30, $edges->getSumCallback('weight'));
    }

    public function testIntersection()
    {
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $e1 = $graph->createEdgeUndirected($v1, $v2);
        $e2 = $graph->createEdgeUndirected($v1, $v2);
        $e3 = $graph->createEdgeUndirected($v1, $v2);

        $edges1 = $this->createEdges(array($e1, $e2));
        $edges2 = $this->createEdges(array($e2, $e3));

        $edges3 = $edges1->getEdgesIntersection($edges2);
        $this->assertCount(1, $edges3);
        $this->assertEquals($e2, $edges3->getEdgeFirst());
    }

    public function testIntersectionDuplicates()
    {
        $graph = new Graph();
        $v1 = $graph->createVertex();
        $v2 = $graph->createVertex();
        $e1 = $graph->createEdgeUndirected($v1, $v2);

        $edges1 = $this->createEdges(array($e1, $e1, $e1));
        $edges2 = $this->createEdges(array($e1, $e1));

        $edges3 = $edges1->getEdgesIntersection($edges2);
        $this->assertCount(2, $edges3);
    }

    public function testIntersectionEmpty()
    {
        $edges1 = new Edges();
        $edges2 = new Edges();

        $edges3 = $edges1->getEdgesIntersection($edges2);
        $this->assertCount(0, $edges3);
    }
}

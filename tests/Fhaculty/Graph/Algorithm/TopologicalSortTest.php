<?php

use Fhaculty\Graph\Algorithm\TopologicalSort;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Graph;

/**
 * Tests for Topological Sort Algorithm
 *
 * To test for Topological Sorting we need to test for Graph having:
 * - no cycles
 * - is directed
 * In short a DirectedAcyclicGraph.
 *
 * @see http://en.wikipedia.org/wiki/Topological_sorting
 * @see http://en.wikipedia.org/wiki/Directed_acyclic_graph
 */
class TopologicalSortTest extends TestCase
{
    public function testGraphEmpty()
    {
        $graph = new Graph();

        $alg = new TopologicalSort($graph);

        $this->assertSame(array(), $alg->getVertices());
    }

    public function testGraphIsolated()
    {
        $graph = new Graph();
        $graph->createVertex(1);
        $graph->createVertex(2);

        $alg = new TopologicalSort($graph);

        $this->assertSame(array(1 => $graph->getVertex(1), 2 => $graph->getVertex(2)), $alg->getVertices());
    }

    /**
     * 1 -> 2
     */
    public function testGraphSimple()
    {
        $graph = new Graph();
        $graph->createVertex(1)->createEdgeTo($graph->createVertex(2));

        $alg = new TopologicalSort($graph);

        $this->assertSame(array(1 => $graph->getVertex(1), 2 => $graph->getVertex(2)), $alg->getVertices());
    }

    /**
     * We test a more complex graph.
     *
     * A -> B -> C
     * P -> Q
     * B -> Q
     * C -> P
     *
     * should result into
     *
     * A -> B -> C
     *              P -> Q
     */
    public function testGraphComplexInOrder() {
        $graph = new Graph();
        $graph->createVertex("A")->createEdgeTo($graph->createVertex("B"));

        $alg = new TopologicalSort($graph);

        $tsl = array_keys($alg->getVertices());
        $this->assertSame(array('A', 'B'), $tsl);

        $graph->getVertex("B")->createEdgeTo($graph->createVertex("C"));
        $tsl = array_keys($alg->getVertices());
        $this->assertSame(array('A', 'B', 'C'), $tsl);

        $graph->createVertex("P")->createEdgeTo($graph->createVertex("Q"));
        $tsl = array_keys($alg->getVertices());
        $this->assertSame(array('A', 'B', 'C', 'P', 'Q'), $tsl);

        $graph->getVertex("B")->createEdgeTo($graph->getVertex("Q"));
        $tsl = array_keys($alg->getVertices());
        $this->assertSame(array('A', 'B', 'C', 'P', 'Q'), $tsl, 'Added B -> Q');

        $graph->getVertex("C")->createEdgeTo($graph->getVertex("P"));
        $tsl = array_keys($alg->getVertices());
        $this->assertSame(array('A', 'B', 'C', 'P', 'Q'), $tsl, "Complete Graph");
    }

    /**
     * We test a more complex graph.
     *
     * A -> B -> C
     * Q -> B
     * A -> P
     *
     * should result into a TSL
     *
     * A ->         B -> C
     *      P -> Q
     */
    public function testGraphComplexOutOfOrder() {
        $graph = new Graph();
        $graph->createVertex("A")->createEdgeTo($graph->createVertex("B"));

        $alg = new TopologicalSort($graph);

        $tsl = array_keys($alg->getVertices());
        $this->assertSame(array('A', 'B'), $tsl);

        $graph->getVertex("B")->createEdgeTo($graph->createVertex("C"));
        $tsl = array_keys($alg->getVertices());
        $this->assertSame(array('A', 'B', 'C'), $tsl);

        $graph->createVertex("P")->createEdgeTo($graph->createVertex("Q"));
        $tsl = array_keys($alg->getVertices());
        $this->assertSame(array('A', 'B', 'C', 'P', 'Q'), $tsl);

        $graph->getVertex("Q")->createEdgeTo($graph->getVertex("B"));
        $tsl = array_keys($alg->getVertices());
        $this->assertSame(array('A', 'P', 'Q', 'B', 'C'), $tsl, 'Added Q -> B');

        $graph->getVertex("A")->createEdgeTo($graph->getVertex("P"));
        $tsl = array_keys($alg->getVertices());
        $this->assertSame(array('A', 'P', 'Q', 'B', 'C'), $tsl, 'Complete Graph');
    }

    /**
     * 1 - 2 : undirected
     *
     * @expectedException UnexpectedValueException
     */
    public function testFailUndirected()
    {
        $graph = new Graph();
        $graph->createVertex(1)->createEdge($graph->createVertex(2));

        $alg = new TopologicalSort($graph);
        $alg->getVertices();
    }

    /**
     * 1 -> 1
     *
     * @expectedException UnexpectedValueException
     */
    public function testFailLoop()
    {
        $graph = new Graph();
        $graph->createVertex(1)->createEdgeTo($graph->getVertex(1));

        $alg = new TopologicalSort($graph);
        $alg->getVertices();
    }

    /**
     * 1 -> 2 -> 1
     *
     * @expectedException UnexpectedValueException
     */
    public function testFailCycle()
    {
        $graph = new Graph();
        $graph->createVertex(1)->createEdgeTo($graph->createVertex(2));
        $graph->getVertex(2)->createEdgeTo($graph->getVertex(1));

        $alg = new TopologicalSort($graph);
        $alg->getVertices();
    }
}

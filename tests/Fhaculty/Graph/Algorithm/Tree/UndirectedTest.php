<?php

use Fhaculty\Graph\Graph;

use Fhaculty\Graph\Algorithm\Tree\Undirected;

class UndirectedTest extends TestCase
{
    protected function createTree(Graph $graph)
    {
        return new Undirected($graph);
    }

    public function testGraphEmpty()
    {
        $graph = new Graph();

        $tree = $this->createTree($graph);

        $this->assertTrue($tree->isTree());
        $this->assertSame(array(), $tree->getVerticesInternal());
        $this->assertSame(array(), $tree->getVerticesLeaf());
    }

    public function testGraphTrivial()
    {
        $graph = new Graph();
        $graph->createVertex('v1');

        $tree = $this->createTree($graph);
        $this->assertTrue($tree->isTree());
        $this->assertSame(array(), $tree->getVerticesInternal());
        $this->assertSame(array(), $tree->getVerticesLeaf());
    }

    public function testGraphSimplePair()
    {
        // v1 -- v2
        $graph = new Graph();
        $graph->createVertex('v1')->createEdge($graph->createVertex('v2'));

        $tree = $this->createTree($graph);
        $this->assertTrue($tree->isTree());
        $this->assertSame(array(), $tree->getVerticesInternal());
        $this->assertSame($graph->getVertices(), $tree->getVerticesLeaf());
    }

    public function testGraphSimpleLine()
    {
        // v1 -- v2 -- v3
        $graph = new Graph();
        $graph->createVertex('v1')->createEdge($graph->createVertex('v2'));
        $graph->getVertex('v2')->createEdge($graph->createVertex('v3'));

        $tree = $this->createTree($graph);
        $this->assertTrue($tree->isTree());
        $this->assertSame(array($graph->getVertex('v2')), array_values($tree->getVerticesInternal()));
        $this->assertSame(array($graph->getVertex('v1'), $graph->getVertex('v3')), array_values($tree->getVerticesLeaf()));
    }

    public function testGraphPairParallelIsNotTree()
    {
        // v1 -- v2 -- v1
        $graph = new Graph();
        $graph->createVertex('v1')->createEdge($graph->createVertex('v2'));
        $graph->getVertex('v1')->createEdge($graph->getVertex('v2'));

        $tree = $this->createTree($graph);
        $this->assertFalse($tree->isTree());
    }

    public function testGraphLoopIsNotTree()
    {
        // v1 -- v1
        $graph = new Graph();
        $graph->createVertex('v1')->createEdge($graph->getVertex('v1'));

        $tree = $this->createTree($graph);
        $this->assertFalse($tree->isTree());
    }

    public function testGraphCycleIsNotTree()
    {
        // v1 -- v2 -- v3 -- v1
        $graph = new Graph();
        $graph->createVertex('v1')->createEdge($graph->createVertex('v2'));
        $graph->getVertex('v2')->createEdge($graph->createVertex('v3'));
        $graph->getVertex('v3')->createEdge($graph->getVertex('v1'));

        $tree = $this->createTree($graph);
        $this->assertFalse($tree->isTree());
    }

    public function testGraphDirectedIsNotTree()
    {
        // v1 -> v2
        $graph = new Graph();
        $graph->createVertex('v1')->createEdgeTo($graph->createVertex('v2'));

        $tree = $this->createTree($graph);
        $this->assertFalse($tree->isTree());
    }

    public function testGraphMixedIsNotTree()
    {
        // v1 -- v2 -> v3
        $graph = new Graph();
        $graph->createVertex('v1')->createEdge($graph->createVertex('v2'));
        $graph->getVertex('v2')->createEdgeTo($graph->createVertex('v3'));

        $tree = $this->createTree($graph);
        $this->assertFalse($tree->isTree());
    }
}

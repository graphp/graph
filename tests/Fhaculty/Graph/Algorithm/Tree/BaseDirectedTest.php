<?php

use Fhaculty\Graph\Algorithm\Tree\BaseDirected;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Graph;

abstract class BaseDirectedTest extends TestCase
{
    abstract protected function createTreeAlg(Graph $graph);

    abstract protected function createGraphNonTree();

    abstract protected function createGraphTree();

    public function testEmptyGraph()
    {
        $graph = new Graph();

        $tree = $this->createTreeAlg($graph);
        $this->assertTrue($tree->isTree());
        $this->assertEquals(array(), $tree->getVerticesLeaf());
        $this->assertEquals(array(), $tree->getVerticesInternal());

        return $tree;
    }

    /**
     * @param BaseDirected $tree
     * @depends testEmptyGraph
     * @expectedException UnderflowException
     */
    public function testEmptyGraphDoesNotHaveRootVertex(BaseDirected $tree)
    {
        $tree->getVertexRoot();
    }

    /**
     * @param BaseDirected $tree
     * @depends testEmptyGraph
     * @expectedException UnderflowException
     */
    public function testEmptyGraphDoesNotHaveDegree(BaseDirected $tree)
    {
        $tree->getDegree();
    }

    /**
     * @param BaseDirected $tree
     * @depends testEmptyGraph
     * @expectedException UnderflowException
     */
    public function testEmptyGraphDoesNotHaveHeight(BaseDirected $tree)
    {
        $tree->getHeight();
    }

    public function testGraphTree()
    {
        $graph = $this->createGraphTree();
        $root = $graph->getVertexFirst();

        $nonRoot = $graph->getVertices();
        unset($nonRoot[$root->getId()]);

        $c1 = current($nonRoot);

        $tree = $this->createTreeAlg($graph);

        $this->assertTrue($tree->isTree());
        $this->assertSame($root, $tree->getVertexRoot());
        $this->assertSame(array_values($nonRoot), array_values($tree->getVerticesChildren($root)));
        $this->assertSame(array_values($nonRoot), array_values($tree->getVerticesLeaf()));
        $this->assertSame(array(), array_values($tree->getVerticesInternal()));
        $this->assertSame($root, $tree->getVertexParent($c1));
        $this->assertSame(array(), $tree->getVerticesChildren($c1));
        $this->assertEquals(2, $tree->getDegree());
        $this->assertEquals(0, $tree->getDepthVertex($root));
        $this->assertEquals(1, $tree->getDepthVertex($c1));
        $this->assertEquals(1, $tree->getHeight());
        $this->assertEquals(1, $tree->getHeightVertex($root));
        $this->assertEquals(0, $tree->getHeightvertex($c1));

        return $tree;
    }

    /**
     *
     * @param BaseDirected $tree
     * @depends testGraphTree
     * @expectedException UnderflowException
     */
    public function testGraphTreeRootDoesNotHaveParent(BaseDirected $tree)
    {
        $root = $tree->getVertexRoot();
        $tree->getVertexParent($root);
    }

    public function testNonTree()
    {
        $graph = $this->createGraphNonTree();

        $tree = $this->createTreeAlg($graph);

        $this->assertFalse($tree->isTree());
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testNonTreeVertexHasMoreThanOneParent()
    {
        $graph = $this->createGraphNonTree();

        $tree = $this->createTreeAlg($graph);

        $tree->getVertexParent($graph->getVertex('v3'));
    }
}

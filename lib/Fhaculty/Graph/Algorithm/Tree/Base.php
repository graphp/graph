<?php

namespace Fhaculty\Graph\Algorithm\Tree;

use Fhaculty\Graph\Algorithm\BaseGraph;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Algorithm\Search\StrictDepthFirst;

/**
 *
 * @link http://en.wikipedia.org/wiki/Tree_%28graph_theory%29
 * @see OutTree
 * @see InTree
 */
abstract class Base extends BaseGraph
{
    /**
     * checks whether the given graph is actually a tree
     *
     * @return boolean
     */
    abstract public function isTree();

    /**
     * checks if the given $vertex is a leaf (outermost vertex / leaf node / external node / terminal node)
     *
     * @param Vertex $vertex
     * @return boolean
     */
    abstract public function isVertexLeaf(Vertex $vertex);

    /**
     * checks if the given $vertex is an internal vertex (inner node / inode / branch node / somewhere in the "middle" of the tree)
     *
     * @param Vertex $vertex
     * @return boolean
     */
    abstract public function isVertexInternal(Vertex $vertex);

    /**
     * get array of leaf vertices (outermost vertices with no children)
     *
     * @return Vertex[]
     * @uses Graph::getVertices()
     * @uses self::isVertexLeaf()
     */
    public function getVerticesLeaf()
    {
        return array_filter($this->graph->getVertices(), array($this, 'isVertexLeaf'));
    }

    /**
     * get array of internal vertices
     *
     * @return Vertex[]
     * @uses Graph::getVertices()
     * @uses self::isVertexInternal()
     */
    public function getVerticesInternal()
    {
        return array_filter($this->graph->getVertices(), array($this, 'isVertexInternal'));
    }
}

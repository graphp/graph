<?php

namespace Fhaculty\Graph\Algorithm\Tree;

use Fhaculty\Graph\Algorithm\BaseGraph;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Algorithm\Search\StrictDepthFirst;
use Fhaculty\Graph\Algorithm\Degree;

/**
 * Abstract base class for tree algorithms
 *
 * This abstract base class provides the base interface for working with
 * graphs that represent a tree.
 *
 * A tree is a connected Graph (single component) with no cycles. Every Tree is
 * a Graph, but not every Graph is a Tree.
 *
 *    A
 *   / \
 *  B   C
 *     / \
 *    D   E
 *
 * Special cases are undirected trees (like the one pictured above), handled via
 * Tree\Undirected and directed, rooted trees (InTree and OutTree), handled via
 * Tree\BaseDirected.
 *
 * @link http://en.wikipedia.org/wiki/Tree_%28graph_theory%29
 * @link http://en.wikipedia.org/wiki/Tree_%28data_structure%29
 * @see Undirected for an implementation of these algorithms on (undirected) trees
 * @see BaseDirected for an abstract implementation of these algorithms on directed, rooted trees
 */
abstract class Base extends BaseGraph
{
    /**
     *
     * @var Degree
     */
    protected $degree;

    public function __construct(Graph $graph)
    {
        parent::__construct($graph);

        $this->degree = new Degree($graph);
    }

    /**
     * checks whether the given graph is actually a tree
     *
     * @return boolean
     */
    abstract public function isTree();

    /**
     * checks if the given $vertex is a leaf (outermost vertext)
     *
     * leaf vertex is also known as leaf node, external node or terminal node
     *
     * @param Vertex $vertex
     * @return boolean
     */
    abstract public function isVertexLeaf(Vertex $vertex);

    /**
     * checks if the given $vertex is an internal vertex (somewhere in the "middle" of the tree)
     *
     * internal vertex is also known as inner node (inode) or branch node
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

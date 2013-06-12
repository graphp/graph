<?php

namespace Fhaculty\Graph\Algorithm\Tree;

use Fhaculty\Graph\Algorithm\Tree\Base as Tree;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Algorithm\Search\Base as Search;
use Fhaculty\Graph\Algorithm\Search\StrictDepthFirst;
use Fhaculty\Graph\Vertex;

/**
 * Undirected tree implementation
 *
 * An undirected tree is a connected Graph (single component) with no cycles.
 * Every undirected Tree is an undirected Graph, but not every undirected Graph
 * is an undirected Tree.
 *
 *    A
 *   / \
 *  B   C
 *     / \
 *    D   E
 *
 * Undirected trees do not have special root Vertices (like the above picture
 * might suggest). The above tree Graph can also be equivalently be pictured
 * like this:
 *
 *      C
 *     /|\
 *    / | \
 *   A  D  E
 *  /
 * B
 *
 * If you're looking for a tree with a designated root Vertex, use directed,
 * rooted trees (BaseDirected).
 *
 * @link http://en.wikipedia.org/wiki/Tree_%28graph_theory%29
 * @see BaseDirected if you're looking for directed, rooted trees
 */
class Undirected extends Tree
{
    public function isTree()
    {
        if ($this->graph->isEmpty()) {
            return true;
        }

        // every vertex can represent a root vertex, so just pick one
        $root = $this->graph->getVertexFirst();

        // TODO: recurse $root to get sub-vertices
        $vertices = array();

        return (count($vertices) === $this->graph->getNumberOfVertices());
    }

    /**
     * checks if the given $vertex is a leaf (outermost vertex with exactly one edge)
     *
     * @param Vertex $vertex
     * @return boolean
     * @uses Vertex::getDegree()
     */
    public function isVertexLeaf(Vertex $vertex)
    {
        return ($vertex->getDegree() === 1);
    }

    /**
     * checks if the given $vertex is an internal vertex (inner vertex with at least 2 edges)
     *
     * @param Vertex $vertex
     * @return boolean
     * @uses Vertex::getDegree()
     */
    public function isVertexInternal(Vertex $vertex)
    {
        return ($vertex->getDegree() >= 2);
    }
}

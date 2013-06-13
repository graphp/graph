<?php

namespace Fhaculty\Graph\Algorithm\Tree;

use Fhaculty\Graph\Algorithm\Tree\Base as Tree;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Algorithm\Search\Base as Search;
use Fhaculty\Graph\Algorithm\Search\StrictDepthFirst;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Edge\UndirectedId as UndirectedEdge;

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
    /**
     * checks if this is a tree
     *
     * @return boolean
     * @uses Graph::isEmpty() to skip empty Graphs (an empty Graph is a valid tree)
     * @uses Graph::getVertexFirst() to get get get random "root" Vertex to start search from
     * @uses self::getVerticesSubtreeRecursive() to count number of vertices connected to root
     */
    public function isTree()
    {
        if ($this->graph->isEmpty()) {
            return true;
        }

        // every vertex can represent a root vertex, so just pick one
        $root = $this->graph->getVertexFirst();

        $vertices = array();
        try {
            $this->getVerticesSubtreeRecursive($root, $vertices, null);
        }
        catch (UnexpectedValueException $e) {
            return false;
        }

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

    /**
     * get subtree for given Vertex and ignore path to "parent" ignoreVertex
     *
     * @param Vertex      $vertex
     * @param Vertex[]    $vertices
     * @param Vertex|null $ignore
     * @throws UnexpectedValueException for cycles or directed edges (check isTree()!)
     * @uses self::getVerticesNeighbor()
     * @uses self::getVerticesSubtreeRecursive() to recurse into sub-subtrees
     */
    private function getVerticesSubtreeRecursive(Vertex $vertex, &$vertices, Vertex $ignore = null)
    {
        if (isset($vertices[$vertex->getId()])) {
            // vertex already visited => must be a cycle
            throw new UnexpectedValueException('Vertex already visited');
        }
        $vertices[$vertex->getId()] = $vertex;

        foreach ($this->getVerticesNeighbor($vertex) as $vertexNeighboor) {
            if ($vertexNeighboor === $ignore) {
                // ignore source vertex only once
                $ignore = null;
                continue;
            }
            $this->getVerticesSubtreeRecursive($vertexNeighboor, $vertices, $vertex);
        }
    }

    /**
     * get neighbor vertices for given start vertex
     *
     * @param Vertex $vertex
     * @throws UnexpectedValueException for directed edges
     * @return Vertex[] (might include possible duplicates)
     * @uses Vertex::getEdges()
     * @uses Edge::getVertexToFrom()
     * @see Vertex::getVerticesEdge()
     */
    private function getVerticesNeighbor(Vertex $vertex)
    {
        $vertices = array();
        foreach ($vertex->getEdges() as $edge) {
            /* @var Edge $edge */
            if (!($edge instanceof UndirectedEdge)) {
                throw new UnexpectedValueException('Directed edge encountered');
            }
            $vertices[] = $edge->getVertexToFrom($vertex);
        }
        return $vertices;
    }
}

<?php

namespace Fhaculty\Graph\Algorithm\MinimumSpanningTree;

use Fhaculty\Graph\Algorithm\Base as AlgorithmBase;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Edge\Directed as EdgeDirected;

/**
 * Abstract base class for minimum spanning tree (MST) algorithms
 *
 * A minimum spanning tree of a graph is a subgraph that is a tree and connects
 * all the vertices together while minimizing the total sum of all edges'
 * weights.
 *
 * A spanning tree thus requires a connected graph (single connected component),
 * otherwise we can span multiple trees (spanning forest) within each component.
 * Because a null graph (a Graph with no vertices) is not considered connected,
 * it also can not contain a spanning tree.
 *
 * @link http://en.wikipedia.org/wiki/Minimum_Spanning_Tree
 * @link http://en.wikipedia.org/wiki/Spanning_Tree
 * @link http://mathoverflow.net/questions/120536/is-the-empty-graph-a-tree
 */
abstract class Base extends AlgorithmBase
{
    /**
     * create new resulting graph with only edges on minimum spanning tree
     *
     * @return Graph
     * @uses self::getGraph()
     * @uses self::getEdges()
     * @uses Graph::createGraphCloneEdges()
     */
    public function createGraph()
    {
        return $this->getGraph()->createGraphCloneEdges($this->getEdges());
    }

    /**
     * get all edges on minimum spanning tree
     *
     * @return Edges
     */
    abstract public function getEdges();

    /**
     * return reference to current Graph
     *
     * @return Graph
     */
    abstract protected function getGraph();
}

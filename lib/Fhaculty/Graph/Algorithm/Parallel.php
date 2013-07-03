<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Algorithm\BaseGraph;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Graph;
use LogicException;

/**
 * Basic algorithms for working with parallel edges
 *
 * Parallel edges (also called multiple edges or a multi-edge), are two or more
 * edges that are incident to the same two vertices. A simple graph has no
 * multiple edges.
 *
 * @link http://en.wikipedia.org/wiki/Multiple_edges
 */
class Parallel extends BaseGraph
{
    /**
     * checks whether this graph has any parallel edges (aka multigraph)
     *
     * @return boolean
     * @uses Edge::hasEdgeParallel() for every edge
     */
    public function hasEdgeParallel()
    {
        foreach ($this->graph->getEdges() as $edge) {
            if ($this->hasEdgeParallelEdge($edge)) {
                return true;
            }
        }

        return false;
    }


    /**
     * checks whether this edge has any parallel edges
     *
     * @return boolean
     * @uses Edge::getEdgesParallel()
     */
    public function hasEdgeParallelEdge(Edge $edge)
    {
        return !!$this->getEdgesParallelEdge($edge);
    }

    /**
     * get all edges parallel to this edge (excluding self)
     *
     * @return Edge[]
     * @throws LogicException
     */
    public function getEdgesParallelEdge(Edge $edge)
    {
        $ends = $edge->getVertices();

        // get all edges between this edge's endpoints
        $edges = $ends[0]->getEdgesTo($ends[1]);
        // edge points into both directions (undirected/bidirectional edge)
        if ($edge->isConnection($ends[1], $ends[0])) {
            // also get all edges in other direction
            $back = $ends[1]->getEdgesTo($ends[0]);
            foreach ($back as $edgee) {
                if (!in_array($edgee, $edges)) {
                    $edges[] = $edgee;
                }
            } // alternative implementation for array_unique(), because it requires casting edges to string
        }

        $pos = array_search($edge, $edges, true);

        if ($pos === false) {
            // @codeCoverageIgnoreStart
            throw new LogicException('Internal error: Current edge not found');
            // @codeCoverageIgnoreEnd
        }

        // exclude current edge from parallel edges
        unset($edges[$pos]);

        return array_values($edges);
    }
}

<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Algorithm\BaseGraph;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Edge\Directed as DirectedEdge;
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
        if ($edge instanceof DirectedEdge) {
            // get all edges between this edge's endpoints
            $edges = $edge->getVertexStart()->getEdgesTo($edge->getVertexEnd());
        } else {
            // edge points into both directions (undirected/bidirectional edge)
            // also get all edges in other direction
            $ends       = $edge->getVertices();
            $edgesOther = $ends[1]->getEdges();

            $edges = array_filter($ends[0]->getEdges(), function(Edge $edge) use ($edgesOther) {
                return in_array($edge, $edgesOther, true);
            });
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

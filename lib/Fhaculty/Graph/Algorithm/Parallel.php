<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Algorithm\BaseGraph;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Graph;

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
            if ($edge->hasEdgeParallel()) {
                return true;
            }
        }

        return false;
    }
}

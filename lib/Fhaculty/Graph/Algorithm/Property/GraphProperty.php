<?php

namespace Fhaculty\Graph\Algorithm\Property;

use Fhaculty\Graph\Algorithm\BaseGraph;

/**
 * Simple algorithms for working with Graph properties
 *
 * @link https://en.wikipedia.org/wiki/Graph_property
 */
class GraphProperty extends BaseGraph
{
    /**
     * checks whether this graph has no edges
     *
     * @return boolean
     */
    public function isEdgeless()
    {
        return $this->graph->getEdges()->isEmpty();
    }

    /**
     * checks whether this graph is trivial (one vertex and no edges)
     *
     * @return boolean
     */
    public function isTrivial()
    {
        return ($this->graph->getEdges()->isEmpty() && $this->graph->getNumberOfVertices() === 1);
    }
}

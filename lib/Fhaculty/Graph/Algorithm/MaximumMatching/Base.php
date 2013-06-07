<?php

namespace Fhaculty\Graph\Algorithm\MaximumMatching;

use Fhaculty\Graph\Algorithm\BaseGraph;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Edge\Base as Edge;

abstract class Base extends BaseGraph
{
    /**
     * Get the count of edges that are in the match
     *
     * @throws Exception
     * @return int
     * @uses Base::getEdges()
     */
    public function getNumberOfMatches()
    {
        return count($this->getEdges());
    }

    /**
     * create new resulting graph with only edges from maximum matching
     *
     * @return Graph
     * @uses Base::getEdges()
     * @uses Graph::createGraphCloneEdges()
     */
    public function createGraph()
    {
        return $this->graph->createGraphCloneEdges($this->getEdges());
    }

    /**
     * create new resulting graph with minimum-cost flow on edges
     *
     * @return Edge[]
     */
    abstract public function getEdges();
}

<?php

namespace Fhaculty\Graph\Algorithm\MinimumSpanningTree;

use Fhaculty\Graph\Algorithm\Base as AlgorithmBase;
use Fhaculty\Graph\Set\Edges;

abstract class Base extends AlgorithmBase
{
    /**
     * create new resulting graph with only edges on minimum spanning tree
     *
     * @return Graph
     * @uses AlgorithmMst::getEdges()
     * @uses Graph::createGraphCloneEdges()
     */
    public function createGraph()
    {
        // Copy Graph
        return $this->getGraph()->createGraphCloneEdges($this->getEdges());
    }

    abstract protected function getGraph();

    /**
     * get all edges on minimum spanning tree
     *
     * @return Edges
     */
    abstract public function getEdges();
}

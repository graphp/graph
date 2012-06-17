<?php

namespace Fhaculty\Graph\Algorithm\MinimumSpanningTree;

use Fhaculty\Graph\Algorithm\Base as AlgorithmBase;

abstract class Base extends AlgorithmBase {
    
    /**
     * create new resulting graph with only edges on minimum spanning tree
     *
     * @return Graph
     * @uses AlgorithmMst::getEdges()
     * @uses Graph::createGraphCloneEdges()
     */
    public function createGraph(){
        return $this->startVertex->getGraph()->createGraphCloneEdges($this->getEdges());                //Copy Graph
    }
    
    /**
     * get all edges on minimum spanning tree
     * 
     * @return array[Edge]
     */
    abstract public function getEdges();
}

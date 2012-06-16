<?php

namespace Fhaculty\Graph\Algorithm;

abstract class Mst extends Base {
    
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

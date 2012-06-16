<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Cycle;

abstract class Tsp extends Base {
    /**
     * get resulting graph with the (first) best circle of edges connecting all vertices
     *
     * @throws Exception on error
     * @return Graph
     * @uses AlgorithmTsp::getEdges()
     * @uses Graph::createGraphCloneEdges()
     */
    public function createGraph(){
        return $this->graph->createGraphCloneEdges($this->getEdges());
    }
    
    abstract protected function getVertexStart();
    
    /**
     * get (first) best circle connecting all vertices
     * 
     * @return Cycle
     * @uses AlgorithmTsp::getEdges()
     * @uses AlgorithmTsp::getVertexStart()
     * @uses Cycle::factoryFromEdges()
     */
    public function getCycle(){
        return Cycle::factoryFromEdges($this->getEdges(),$this->getVertexStart());
    }
    
    /**
     * get array of edges connecting all vertices in a circle
     * 
     * @return array[Edge]
     */
    abstract public function getEdges();
}

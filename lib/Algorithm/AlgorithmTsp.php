<?php

abstract class AlgorithmTsp extends Algorithm {
    /**
     * get resulting graph with the (first) best circle of edges connecting all vertices
     *
     * @throws Exception on error
     * @return Graph
     * @uses AlgorithmTsp::getEdges()
     * @uses Graph::createGraphCloneEdges()
     */
    public function getResultGraph(){
    	return $this->graph->createGraphCloneEdges($this->getEdges());
    }
    
    /**
     * get array of edges connecting all vertices in a circle
     * 
     * @return array[Edge]
     */
    abstract public function getEdges();
}

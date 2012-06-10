<?php

abstract class AlgorithmMM extends Algorithm {

    /**
     * Origianl graph
     * 
     * @var Graph
     */
    protected $graph;
    
    /**
     * The given graph where the algorithm should operate on
     * 
     * @param Graph $graph
     * @throws Exception if the given graph is not balanced
     */
    public function __construct(Graph $graph){
        $this->graph = $graph;
    }
    
    /**
     * Get the count of edges that are in the matchin
     * 
     * @throws Exception
     * @return AlgorithmMCF $this (chainable)
     */
    protected function getMatchingValue(){
        // TODO count the matching edges

        return null;
    }
    
   
    /**
     * create new resulting graph with minimum-cost flow on edges
     *
     * @throws Exception if the graph has not enough capacity for the minimum-cost flow
     * @return Graph
     */
    abstract public function createGraph();
}

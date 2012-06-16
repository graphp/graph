<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Graph;

abstract class MM extends Base {

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
     * Get the count of edges that are in the match
     * 
     * @throws Exception
     * @return AlgorithmMCF $this (chainable)
     * @uses AlgorithmMM::createGraph()
     * @uses Graph::getNumberOfEdges()
     */
    public function getNumberOfMatches(){
        return $this->createGraph()->getNumberOfEdges();
    }
    
   
    /**
     * create new resulting graph with minimum-cost flow on edges
     *
     * @throws Exception if the graph has not enough capacity for the minimum-cost flow
     * @return Graph
     */
    abstract public function createGraph();
}

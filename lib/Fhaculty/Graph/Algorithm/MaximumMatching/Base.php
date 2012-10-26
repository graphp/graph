<?php

namespace Fhaculty\Graph\Algorithm\MaximumMatching;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Algorithm\Base as AlgorithmBase;

abstract class Base extends AlgorithmBase {

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
     * @return int
     * @uses Base::getEdges()
     */
    public function getNumberOfMatches(){
        return count($this->getEdges());
    }

    /**
     * create new resulting graph with only edges from maximum matching
     *
     * @return Graph
     * @uses Base::getEdges()
     * @uses Graph::createGraphCloneEdges()
     */
    public function createGraph(){
    	return $this->graph->createGraphCloneEdges($this->getEdges());
    }
   
    /**
     * create new resulting graph with minimum-cost flow on edges
     *
     * @return Edge[]
     */
    abstract public function getEdges();
}

<?php

abstract class AlgorithmMCF extends Algorithm {

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
    	
    	//Check if balance is ok
    	$vertices = $this->graph->getVertices();
    	$balance = 0;
    	foreach ($vertices as $vertex) {										//Sum for all vertices of value
    		$balance += $vertex->getValue();
    	}
    	$toleranz=0;
    	if (($balance > 0+$toleranz || $balance < 0-$toleranz)) {													//If the sum is 0 => same "in-flow" as "out-flow"
    		throw new Exception("The given graph is not balanced value is: ".$balance);
    	}
    	
    }
    
   
    
    /**
     * create new resulting graph with minimum-cost flow on edges
     *
     * @throws Exception if the graph has not enough capacity for the minimum-cost flow
     * @return Graph
     */
    abstract public function createGraph();
}

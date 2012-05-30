<?php

abstract class AlgorithmMCF extends Algorithm {

	/**
	 * Origianl graph
	 * 
	 * @var Graph
	 */
	protected $graph;
	
	/**
	 * 
	 * @var unknown_type
	 */
	protected $resultGraph;
	
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
    	if (($balance > 0 || $balance < 0)) {													//If the sum is 0 => same "in-flow" as "out-flow"
    		throw new Exception("The given graph is not balanced value is: ".$balance);
    	}
    	$resultGraph = $this->graph->createGraphClone();
    	//preset the flow with the capacity values
    	$resultGraph->initFlow();
    }
    
   
    
    /**
     * create new resulting graph with minimum-cost flow on edges
     *
     * @return Graph
     */
    abstract public function getResultGraph();
}

<?php

abstract class AlgorithmMCF extends Algorithm {

	/**
	 * Origianl graph
	 * 
	 * @var Graph
	 */
	protected $graph;
	
    public function __construct(Graph $graph){
    	$this->graph = $graph;
    	
    	//Check if balance is ok
    	$vertices = $this->graph->getVertices();
    	$balance = 0;
    	foreach ($vertices as $vertex) {										//Sum for all vertices of value
    		$balance += $vertex->getValue();
    	}
    	if ($balance !== 0) {													//If the sum is 0 => same "in-flow" as "out-flow"
    		throw new Exception("The graph is not balanced");
    	}
    }
    
    /**
     * create new resulting graph with minimum-cost flow on edges
     *
     * @return Graph
     */
    abstract public function getResultGraph();
}

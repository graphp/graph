<?php

abstract class AlgorithmMCF extends Algorithm {

	/**
	 * Origianl graph
	 * 
	 * @var Graph
	 */
	private $graph;
	
    public function __construct(Graph $graph){
    	$this->graph = $graph;
    }
    
    /**
     * create new resulting graph with minimum-cost flow on edges
     *
     * @return Graph
     */
    abstract public function getResultGraph();
}

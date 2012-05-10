<?php

class AlgorithmDetectNegativeCycles{
	
	/**
	 * 
	 * @var Graph
	 */
	private $graph;
	
	/**
	 * 
	 * @param Graph $graph
	 */
	public function __construct(Graph $graph){
		$this->graph = $graph;
	}
	
	/**
	 * Calculates all negative cycles in the graph
	 * 
	 * @param Vertex $startVertex optional
	 * 
	 * @return array[Graph]
	 */
	public function getNegativeCycles(Vertex $startVertex = NULL){
		if ($startVertex == NULL){
			$startVertex = $this->graph->getAnyVertex();
		}
		
		return null;
	}
	
}
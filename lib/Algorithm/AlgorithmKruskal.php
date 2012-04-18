<?php
class AlgorithmKruskal{
	public function __construct(Graph $inputGraph, Vertex $startVertice){
 		$this->startGraph = clone $inputGraph;
 		$this->startVertice = clone $startVertice;
	}

	private $debugMode = false;
	/**
	 *
	 * @return Graph
	 */
	public function getResultGraph(){

		// Initialize program
// 		$returnGraph =  new Graph();

// 		$edgeQueue = new SplPriorityQueue();
// 		$edgeQueue->setExtractFlags(SplPriorityQueue::EXTR_DATA); // Set extract type to value
// 		// END Initialize program



	}

}

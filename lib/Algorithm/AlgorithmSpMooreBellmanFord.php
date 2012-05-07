<?php
class AlgorithmSpDijkstra{
	public function __construct(Graph $inputGraph, Vertex $startVertex){
		$this->startGraph = $inputGraph;
		$this->startVertex = $startVertex;
	}

	/**
	 *
	 * @return Graph
	 */
	public function getResultGraph(){
		return null;
	}
}
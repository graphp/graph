<?php
class AlgorithmSearchBreadthFirst{
	public function __construct(Graph $inputGraph, Vertex $startVertice){
		$this->startGraph = clone $inputGraph;
		$this->startVertice = clone $startVertice;
		;
	}


	/**
	 *
	 * @return Graph[Vertex]
	 */
	public function getVertices(){
		$returnGraph =  new Graph();
		$returnGraph->createVertex($this->startVertice->getId()); // Add starting vertex

		$edgeQueue = new SplPriorityQueue();
		$edgeQueue->setExtractFlags(0x00000001); // Set extract flag to value
		
		$markInserted = array($this->startVertice->getId() => true);

		$allVertices = $this->startGraph->getVertices();
		
		// for all vertices add one edge
		foreach ($allVertices as $currentVertex) { 
	
			// Add edges from $currentVertex to priority queue
			foreach ($currentVertex->getEdges() as $currentEdge) {
				$edgeQueue->insert($currentEdge, $currentEdge->value);
			}

			// Now find next cheapest edge to add
			$cheapestEdge = $edgeQueue->extract();

			// Check if is: [visiteted]->[unvisited]
			$cheapestEdgeIsOk = false;
			while($cheapestEdgeIsOk == false) { 
				foreach ($cheapestEdge->getTargetVertices() as $currentTarget){
					if($markInserted[$currentTarget->getId()] == false){
						$cheapestEdgeIsOk == true;
					}
				}
				$cheapestEdge = $edgeQueue->extract();
			}

			$returnGraph->addEdge($cheapestEdge);
			$markInserted[$currentVertex->getId()] = true;
		}

		return $returnGraph;
	}

}
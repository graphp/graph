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

		// Initialise programm
		$returnGraph = $this->startGraph->createGraphCloneEdgeless();

		$distanceFromStartTo  = Array();
		$distanceFromStartTo[$this->startVertex->getId()] = 0;

		$predecessorTo  = Array();
		$predecessorTo[$this->startVertex->getId()] = $this->startVertex;

		$marked  = Array();

		$nodePriorityQueue = new SplPriorityQueue();

		// Add reachable Nodes
		foreach ($this->startVertex->getEdges() as $edge){
			foreach ($edge->getVertexToFrom($this->startVertex) as $targetVertex){
				$distanceFromStartTo[$targetVertex->getId()] = $edge->getWeight();
				$predecessorTo[$targetVertex->getId()] = $this->startVertex;
				$nodePriorityQueue->insert($targetVertex, -$edge->getWeight());
			}
		}


		// Repeat until all vertices have been marked
		$countOfVertices = 0;
		$totalCountOfVertices = $this->startGraph->count();
		while ($countOfVertices< $totalCountOfVertices){

			// Get next cheapest reachable node from priolist
			$currentVertex = $nodePriorityQueue->extract();

			$currentPredesessor = $predecessorTo[$currentVertex->getId()]; 			// mark predessessor (his cheapest path has been found)
			if(!$marked[$currentPredesessor->getId()]){
				$marked[$currentPredesessor->getId()] = true;
				$countOfVertices++;													// only rise counter if fresh marked
			}
			

			foreach ($currentVertex->getEdges() as $edge){ 											// Add reachable nodes from currently added node and refresh the current possible distances
				foreach ($edge->getVertexToFrom($currentVertex) as $targetVertex){

					$costsToTargetVertex = $distanceFromStartTo[$currentVertex->getId()] + $edge->getWeight();

					if(!isset($distanceFromStartTo[$targetVertex->getId()])){						// if not yet added -> add
						
						$distanceFromStartTo[$targetVertex->getId()] = $costsToTargetVertex;
						$predecessorTo[$targetVertex->getId()] = $currentVertex;
						
						$nodePriorityQueue->insert($targetVertex, -$costsToTargetVertex);
						
					} else if($distanceFromStartTo[$targetVertex->getId()] > $costsToTargetVertex){ // if new costs are lower -> update
						
						$distanceFromStartTo[$targetVertex->getId()] = $costsToTargetVertex;
						$predecessorTo[$targetVertex->getId()] = $currentVertex;
						
						// TODO UPDATE VALUE IN PRIOQUEUE
						// remove element from queue
						// and add with new value
					}
				}
			}
		}
		
		// TODO Add correct edges to return graph or define better output (ex. costs and predesessor list)
	}
}
<?php
class AlgorithmPrim{
	public function __construct(Graph $inputGraph, Vertex $startVertice){
		$this->startGraph = clone $inputGraph;
		$this->startVertice = clone $startVertice;
	}


	/**
	 *
	 * @return Graph[Vertex]
	 */
	public function getVertices(){
		
		// Initialize program
		$returnGraph =  new Graph();
		$returnGraph->createVertex($this->startVertice->getId()); // Add starting vertex

		$edgeQueue = new SplPriorityQueue();
		$edgeQueue->setExtractFlags(0x00000001); // Set extract type to value
		// END Initialize program
		
		
		// Initialize algorithm
		foreach ($this->startVertice->getEdges() as $currentEdge) {
			print "Init adding Edge: ".$currentEdge->toString()."\n";
			$edgeQueue->insert($currentEdge, $currentEdge->value);
		}
		$returnGraph->addEdge($edgeQueue->extract());
		$markInserted = array($this->startVertice->getId() => true);
		// END Initialize algorithm
		
		
		// BEGIN algorithm
		// for all vertices add one edge
		$allVertices = $this->startGraph->getVertices();
		for ($i = 1;$i <= $allVertices;$i++) {
			
			// Find next cheapest edge to add
			$cheapestEdge = $edgeQueue->extract();
			
			// Check if edge is is: [visiteted]->[unvisited]
			$cheapestEdgeIsOk = false;
			while($cheapestEdgeIsOk == false) {
				print "\t\t Checking: ".$cheapestEdge->toString();
				foreach ($cheapestEdge->getTargetVertices() as $currentTarget){
			
					if($currentTarget != $currentVertex){
						if(!isset($markInserted[$currentTarget->getId()])){
							print "\t using it \n";
							$cheapestEdgeIsOk = true;
						} else {
							print "\t target ".$currentTarget->getId()." is already in \n";
						}
					} else {
						print "\t target ".$currentTarget->getId()." is == currentVertex \n";
					}
			
				}
				if($cheapestEdgeIsOk == false){
					$cheapestEdge = $edgeQueue->extract();
				}
			}
			
			print "Choosed cheapest edge: ".$cheapestEdge->toString()."\n\n";
			
			$returnGraph->addEdge($cheapestEdge);
			$markInserted[$currentVertex->getId()] = true;
			
			
			// get unvisited vertex of the edge and add edges from this vertex
			if($currentVertex->getId() != $this->startVertice->getId()){

				print "Adding Vertex with ID:".$currentVertex->getId()."\n";
				// Add all edges from $currentVertex to priority queue
				foreach ($currentVertex->getEdges() as $currentEdge) {
					print "\t Adding Edge: ".$currentEdge->toString()."\n";
					$edgeQueue->insert($currentEdge, $currentEdge->value);
				}
			}
		}
		// END algorithm

		return $returnGraph;
	}

}
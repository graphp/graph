<?php
class AlgorithmPrim{
	public function __construct(Graph $inputGraph, Vertex $startVertice){
		$this->startGraph = clone $inputGraph;
		$this->startVertice = clone $startVertice;
	}


	/**
	 *
	 * @return Graph
	 */
	public function getResultGraph(){

		// Initialize program
		$returnGraph =  new Graph();

		$edgeQueue = new SplPriorityQueue();
		$edgeQueue->setExtractFlags(SplPriorityQueue::EXTR_DATA); // Set extract type to value
		// END Initialize program


		// Initialize algorithm
		print "Init start vertex: ".$this->startVertice->getId()."\n";
		$returnGraph->createVertex($this->startVertice->getId()); // Add starting vertex
		$markInserted = array($this->startVertice->getId() => true);
		foreach ($this->startVertice->getEdges() as $currentEdge) {
			print "\t Init adding Edge: ".$currentEdge->toString()."\n";
			$edgeQueue->insert($currentEdge, -$currentEdge->value);
		}
		// END Initialize algorithm


		// BEGIN algorithm
		// for all vertices add one edge
		//$allVerticesCount = $this->startGraph->getVertices();
		//for ($i = 1;$i < $allVerticesCount;$i++) {
		foreach ($this->startGraph->getVertices() as $value) {
			if($value->getId() == $this->startVertice->getId()){
				continue;
			}
			// Find next cheapest edge to add
			$cheapestEdge = $edgeQueue->extract();
				
			// Check if edge is is: [visiteted]->[unvisited]
			$cheapestEdgeIsOk = false;
			while($cheapestEdgeIsOk == false) {
				print "\t Checking: ".$cheapestEdge->toString()."\n";
					
				foreach ($cheapestEdge->getTargetVertices() as $currentTarget){
						
					$cheapestEdgeIsOkOld = $cheapestEdgeIsOk;
						
					$cheapestEdgeIsOk = $cheapestEdgeIsOk ? true : !isset($markInserted[$currentTarget->getId()]);
						
					if($cheapestEdgeIsOkOld != $cheapestEdgeIsOk){
						$newTargetVertex = $currentTarget;
					}
					// Get the correct target vertex
						
					// 					if($currentTarget != $currentVertex){
					// 						if(!isset($markInserted[$currentTarget->getId()])){
					// 							print "\t using it \n";
					// 							$cheapestEdgeIsOk = true;
					// 						} else {
					// 							print "\t target ".$currentTarget->getId()." is already in \n";
					// 						}
					// 					} else {
					// 						print "\t target ".$currentTarget->getId()." is == currentVertex \n";
					// 					}
							
				}
				if($cheapestEdgeIsOk == false || $newTargetVertex->getId() == $this->startVertice->getId()){
					$cheapestEdge = $edgeQueue->extract();
				}
			}
				
			

			$returnGraph->createVertex($newTargetVertex->getId());
				
			$to = $returnGraph->getVertex($newTargetVertex->getId());
			$fromTemp = $cheapestEdge->getVertexFromToById($to);
			$from = $returnGraph->getVertex($fromTemp->getId());
			
			$newEgde = $returnGraph->addEdge(new EdgeUndirected($to, $from, $cheapestEdge->value));
			
			$to->addEdge($newEgde);
			$from->addEdge($newEgde);
			$returnGraph->addEdge($newEgde);
			
			$markInserted[$newTargetVertex->getId()] = true;
			print "\t\t Choosed cheapest edge: ".$newEgde->toString()."\n";
				
			// get unvisited vertex of the edge and add edges from this vertex
			if($newTargetVertex->getId() != $this->startVertice->getId()){
				print "Adding Vertex with ID:".$newTargetVertex->getId()."\n";
				// Add all edges from $currentVertex to priority queue
				foreach ($newTargetVertex->getEdges() as $currentEdge) {
					print "\t Adding Edge: ".$currentEdge->toString()."\n";
					$edgeQueue->insert($currentEdge, -$currentEdge->value);
				}
			}
		}
		// END algorithm
		
		print "done\n";
		return $returnGraph;
	}

}

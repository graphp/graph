<?php
class AlgorithmPrim{
	public function __construct(Graph $inputGraph, Vertex $startVertice){
		$this->startGraph = $inputGraph;
		$this->startVertice = $startVertice;
	}

	private $debugMode = false;
	/**
	 *
	 * @return Graph
	 */
	public function getResultGraph(){

		// Initialize program
		$returnGraph =  new Graph();

		$edgeQueue = new SplPriorityQueue();
		//default: $edgeQueue->setExtractFlags(SplPriorityQueue::EXTR_DATA);
		// END Initialize program
		

		// Initialize algorithm
		if($this->debugMode){
			print "Init start vertex: ".$this->startVertice->getId()."\n";
		}
		$returnGraph->createVertex($this->startVertice->getId()); // Add starting vertex
		$markInserted = array($this->startVertice->getId() => true);
		foreach ($this->startVertice->getEdges() as $currentEdge) {
			if($this->debugMode){
				print "\t Init adding Edge: ".$currentEdge->toString()."\n";
			}
			$edgeQueue->insert($currentEdge, -$currentEdge->getWeight());
		}
		// END Initialize algorithm


		// BEGIN algorithm
		// for all vertices add one edge
		$startVerticeId = $this->startVertice->getId();
		foreach ($this->startGraph->getVertices() as $value) {
			if($value->getId() == $startVerticeId){
				continue;
			}
			
			// Find next cheapest edge to add
			$cheapestEdge = $edgeQueue->extract();

			// BEGIN Check if edge is is: [visiteted]->[unvisited]
			$cheapestEdgeIsOk = false;
			while($cheapestEdgeIsOk == false) {
				if($this->debugMode){
					print "\t Checking: ".$cheapestEdge->toString()."\n";
				}
					
				foreach ($cheapestEdge->getTargetVertices() as $currentTarget){

					$cheapestEdgeIsOkOld = $cheapestEdgeIsOk;

					$cheapestEdgeIsOk = $cheapestEdgeIsOk ? true : !isset($markInserted[$currentTarget->getId()]);

					if($cheapestEdgeIsOkOld != $cheapestEdgeIsOk){
						$newTargetVertex = $currentTarget;
					}
				}
				if($cheapestEdgeIsOk == false || $newTargetVertex->getId() == $this->startVertice->getId()){
					$cheapestEdge = $edgeQueue->extract();
				}
			}
			// END Check if edge is is: [visiteted]->[unvisited]

			
			// BEGIN Cheapest Edge found, add new vertex and edge to returnGraph
			if($this->debugMode){
				print "\t\t Choosed cheapest edge: ".$newEdge->toString()."\n";
			}
			
			$returnGraph->createVertex($newTargetVertex->getId());
			$markInserted[$newTargetVertex->getId()] = true;
				
			$to = $returnGraph->getVertex($newTargetVertex->getId());
			$fromTemp = $cheapestEdge->getVertexFromToById($to);
			$from = $returnGraph->getVertex($fromTemp->getId());

			$newEdge = $returnGraph->addEdge(new EdgeUndirected($to, $from));
			$newEdge->setWeight($cheapestEdge->getWeight());

			$to->addEdge($newEdge);
			$from->addEdge($newEdge);
			$returnGraph->addEdge($newEdge);
			// END Cheapest Edge found, add new vertex and edge to returnGraph
			


			// BEGIN get unvisited vertex of the edge and add edges from new vertex
			if($newTargetVertex->getId() != $this->startVertice->getId()){
				if($this->debugMode){
					print "Adding Vertex with ID:".$newTargetVertex->getId()."\n";
				}
				// Add all edges from $currentVertex to priority queue
				foreach ($newTargetVertex->getEdges() as $currentEdge) {
					if($this->debugMode){
						print "\t Adding Edge: ".$currentEdge->toString()."\n";
					}
					$edgeQueue->insert($currentEdge, -$currentEdge->getWeight());
				}
			}
			// END get unvisited vertex of the edge and add edges from new vertex
		}
		// END algorithm

		print 'done\n';
		return $returnGraph;
	}

}

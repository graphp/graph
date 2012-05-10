<?php
class AlgorithmMstPrim{
    /**
     * @var Vertex
     */
    private $startVertice;
    
	public function __construct(Vertex $startVertice){
		$this->startVertice = $startVertice;
	}

	private $debugMode = false;
	/**
	 *
	 * @return Graph
	 */
	public function getResultGraph(){

		// Initialize program
		$returnGraph = $this->startVertice->getGraph()->createGraphCloneEdgeless();
		
		$edgeQueue = new SplPriorityQueue();
									
		// END Initialize program
		

		//debug output?
		if($this->debugMode){
			print "Init start vertex: ".$this->startVertice->getId()."\n";
		}
		
		// Initialize algorithm 
		
		$markInserted = array($this->startVertice->getId() => true);			// Color starting vertex
					
		foreach ($this->startVertice->getEdges() as $currentEdge) {				// Add all edges from startvertex
			if($this->debugMode){
				print "\t Init adding Edge: ".$currentEdge->toString()."\n";	
			}
			$edgeQueue->insert($currentEdge, -$currentEdge->getWeight());		// Add edges to priority queue with inverted weights (priority queue has high values at the front)
		}
		// END Initialize algorithm


		// BEGIN algorithm
		
		$startVerticeId = $this->startVertice->getId();							// set start vertex id 
 		foreach ($this->startVertice->getGraph()->getVertices() as $value) {					// iterate n times over edges form know nodes
			
 			if($value->getId() == $startVerticeId){								// skip the first entry to run only n-1 times 
				continue;
			}
			
			
			$cheapestEdge = $edgeQueue->extract();								// Get next cheapest edge

			// BEGIN Check if edge is is: [visiteted]->[unvisited]
			$cheapestEdgeIsOk = false;											// 
			while($cheapestEdgeIsOk == false) {
				if($this->debugMode){
					print "\t Checking: ".$cheapestEdge->toString()."\n";
				}
					
				foreach ($cheapestEdge->getTargetVertices() as $currentTarget){	// run over both vertices

					$cheapestEdgeIsOkOld = $cheapestEdgeIsOk;					

					$cheapestEdgeIsOk = $cheapestEdgeIsOk ? true : !isset($markInserted[$currentTarget->getId()]); //check if already visited, if not visit

					if($cheapestEdgeIsOkOld != $cheapestEdgeIsOk){				//get unvisted vertex				
						$newTargetVertex = $currentTarget;
					}
				}
				if($cheapestEdgeIsOk == false){									// check if cheapest edge is false
					$cheapestEdge = $edgeQueue->extract();						//if edge is not ok, get a new edge from the queue
				}
			}
			// END Check if edge is is: [visiteted]->[unvisited]

			
			// BEGIN Cheapest Edge found, add new vertex and edge to returnGraph
			if($this->debugMode){
				print "\t\t Choosed cheapest edge: ".$newEdge->toString()."\n";
			}
			
			$markInserted[$newTargetVertex->getId()] = true;
				
			$newEgde = $returnGraph->createEdgeClone($cheapestEdge);

			// BEGIN get unvisited vertex of the edge and add edges from new vertex
			if($newTargetVertex->getId() != $this->startVertice->getId()){
				if($this->debugMode){
					print "Adding Vertex with ID:".$newTargetVertex->getId()."\n";
				}
						
				foreach ($newTargetVertex->getEdges() as $currentEdge) {		// Add all edges from $currentVertex to priority queue
					if($this->debugMode){
						print "\t Adding Edge: ".$currentEdge->toString()."\n";
					}
					$edgeQueue->insert($currentEdge, -$currentEdge->getWeight());
				}
			}
			// END get unvisited vertex of the edge and add edges from new vertex
		}
		// END algorithm

		if($this->debugMode){
		    print "done".PHP_EOL;
		}
		return $returnGraph;
	}

}

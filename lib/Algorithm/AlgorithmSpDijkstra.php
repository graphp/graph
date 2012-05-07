<?php
class AlgorithmSpDijkstra{
	
	private $graph;
	private $startVertex;
	
	public function __construct(Graph $inputGraph){
		$this->graph = $inputGraph;
	}
	
	/**
	 *
	 * @return Graph
	 */
	public function getResultGraph(Vertex $startVertex){
		$this->startVertex = $startVertex;
		
		$totalCostOfCheapestPathTo  = Array();
		$totalCostOfCheapestPathTo[$this->startVertex->getId()] = 0;			//Start node distance

		$cheapestVertex = new SplPriorityQueue();								//just to get the cheapest vertex in the right order
		$cheapestVertex->insert($this->startVertex, 0);
		
		$predecesVertexOfCheapestPathTo  = Array();								//Vorgänger
		$predecesVertexOfCheapestPathTo[$this->startVertex->getId()] = $this->startVertex;				

		$usedVertices  = Array();

		// Repeat until all vertices have been marked
		$totalCountOfVertices = $this->graph->count();
		for ($i = 0; $i < $totalCountOfVertices; ++$i){

			$isEmpty = false;
			do{
				if ($cheapestVertex->isEmpty()){								//if the priority queue is empty there are isolated vertices but the algorithem visited all other vertices
					$isEmpty = true;
					break;
				}
				$currentVertex = $cheapestVertex->extract();					//Get cheapest unmarked vertex
			}while( isset($usedVertices[$currentVertex->getId()]) );			//Vertices can be multiple times in the priorety queue with different path costs (if vertex is allready marked this position is an old unvalid value)
			
			if ($isEmpty){														//algorithem is done
				break;
			}
			
			$usedVertices[$currentVertex->getId()] = true;						//mark this vertex
			
			foreach ($currentVertex->getOutgoingEdges() as $edge){ 				//check for all edges of current vertex if there is a cheaper path IN OTHER WORDS Add reachable nodes from currently added node and refresh the current possible distances
				$targetVertex = $edge->getVertexToFrom($currentVertex);
				
				if ( ! isset( $usedVertices[$targetVertex->getId()] ) )			//if the targetVertex is marked, the cheapest path for this vertex is allready found (no negatives edges)
				{
					$newCostsToTargetVertex = $totalCostOfCheapestPathTo[$currentVertex->getId()] + $edge->getWeight();	//calculate new cost to vertex
					
					if ( ( ! isset($predecesVertexOfCheapestPathTo[$targetVertex->getId()]) )
							|| $totalCostOfCheapestPathTo[$targetVertex->getId()] > $newCostsToTargetVertex){	//is the new path cheaper?
						
						$cheapestVertex->insert($targetVertex, - $newCostsToTargetVertex);			//Not an update just a new insert
																									//With lower cost
																									//so the lowest cost will be extraced first
																									//and higher cost skipped during extraction
						
						$totalCostOfCheapestPathTo[$targetVertex->getId()] = $newCostsToTargetVertex;	//set cost of the target vertex to new cheaper path
						$predecesVertexOfCheapestPathTo[$targetVertex->getId()] = $currentVertex;		//set predecessor vertex of cheaper path
					}
				}
			}
		}
		
		//algorithm is done, build graph
		$returnGraph = $this->graph->createGraphCloneEdgeless();				//Copy Graph
		foreach($this->graph->getVertices() as $vertex){
			echo $vertex->getId()." : ".$this->startVertex->getId()."\n";
			if ( $vertex !== $this->startVertex ){								//start vertex doesn't have a predecessor
				if (isset( $predecesVertexOfCheapestPathTo[$vertex->getId()] )){
					$predecesVertex = $predecesVertexOfCheapestPathTo[$vertex->getId()];	//get predecor
					
					echo "EDGE FROM ".$predecesVertex->getId()." TO ".$vertex->getId()." WITH KOST: ".$totalCostOfCheapestPathTo[$vertex->getId()]."\n";
					
					$edge = $predecesVertex->getCheapestEdgeTo($vertex);					//get cheapest edge
					$returnGraph->createEdgeClone($edge);									//clone this edge
				}
			}
		}
		
		return $returnGraph;
	}
}
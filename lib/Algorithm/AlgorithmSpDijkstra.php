<?php
class AlgorithmSpDijkstra{
	
	private $startVertex;
	
	public function __construct(Vertex $startVertex){
		$this->startVertex = $startVertex;
	}
	
	/**
	 * get all edges on shortest path for this vertex
	 * 
	 * @return array[Edge]
	 */
	public function getEdges(){
		$totalCostOfCheapestPathTo  = Array();
		$totalCostOfCheapestPathTo[$this->startVertex->getId()] = 0;			//Start node distance

		$cheapestVertex = new SplPriorityQueue();								//just to get the cheapest vertex in the right order
		$cheapestVertex->insert($this->startVertex, 0);
		
		$predecesVertexOfCheapestPathTo  = Array();								//Vorgänger
		$predecesVertexOfCheapestPathTo[$this->startVertex->getId()] = $this->startVertex;				

		$usedVertices  = Array();
		

		// Repeat until all vertices have been marked
		$totalCountOfVertices = $this->startVertex->getGraph()->getNumberOfVertices();
		for ($i = 0; $i < $totalCountOfVertices; ++$i){
            $currentVertex = NULL;
            $currentVertexId = NULL;
			$isEmpty = false;
			do{
				if ($cheapestVertex->isEmpty()){								//if the priority queue is empty there are isolated vertices but the algorithem visited all other vertices
					$isEmpty = true;
					break;
				}
				$currentVertex = $cheapestVertex->extract();					//Get cheapest unmarked vertex
				$currentVertexId = $currentVertex->getId();
			}while( isset($usedVertices[$currentVertexId]) );			        //Vertices can be multiple times in the priorety queue with different path costs (if vertex is allready marked this position is an old unvalid value)
			
			if ($isEmpty){														//algorithem is done
				break;
			}
			
			$usedVertices[$currentVertexId] = true;				        		//mark this vertex
			
			foreach ($currentVertex->getOutgoingEdges() as $edge){ 				//check for all edges of current vertex if there is a cheaper path IN OTHER WORDS Add reachable nodes from currently added node and refresh the current possible distances
				$targetVertex = $edge->getVertexToFrom($currentVertex);
				$targetVertexId = $targetVertex->getId();
				
				if ( ! isset( $usedVertices[$targetVertexId] ) )			        //if the targetVertex is marked, the cheapest path for this vertex is allready found (no negatives edges)
				{
					$newCostsToTargetVertex = $totalCostOfCheapestPathTo[$currentVertexId] + $edge->getWeight();	//calculate new cost to vertex
					
					if ( ( ! isset($predecesVertexOfCheapestPathTo[$targetVertexId]) )
							|| $totalCostOfCheapestPathTo[$targetVertexId] > $newCostsToTargetVertex){	//is the new path cheaper?
						
						$cheapestVertex->insert($targetVertex, - $newCostsToTargetVertex);			//Not an update just a new insert
																									//With lower cost
																									//so the lowest cost will be extraced first
																									//and higher cost skipped during extraction
						
						$totalCostOfCheapestPathTo[$targetVertexId] = $newCostsToTargetVertex;	//set cost of the target vertex to new cheaper path
						$predecesVertexOfCheapestPathTo[$targetVertexId] = $currentVertex;		//set predecessor vertex of cheaper path
					}
				}
			}
		}
		
		//algorithm is done, return resulting edges
		
		$vertices = $this->startVertex->getGraph()->getVertices();
		unset($vertices[$this->startVertex->getId()]);                          //start vertex doesn't have a predecessor
		
		$edges = array();
		foreach($vertices as $vid=>$vertex){
			//echo $vertex->getId()." : ".$this->startVertex->getId()."\n";
			if (isset( $predecesVertexOfCheapestPathTo[$vid] )){
				$predecesVertex = $predecesVertexOfCheapestPathTo[$vid];	//get predecor
				
				//echo "EDGE FROM ".$predecesVertex->getId()." TO ".$vertex->getId()." WITH KOST: ".$totalCostOfCheapestPathTo[$vertex->getId()]."\n";
				
				$edges []= Edge::getFirst($predecesVertex->getEdgesTo($vertex),Edge::ORDER_WEIGHT);	//get cheapest edge
			}
		}
		
		return $edges;
	}
	
	/**
	 * create new resulting graph with only edges on shortest path
	 * 
	 * @return Graph
	 * @uses AlgorithmSpDijkstra::getEdges()
	 * @uses Graph::createGraphCloneEdges()
	 */
	public function getResultGraph(){
	    return $this->startVertex->getGraph()->createGraphCloneEdges($this->getEdges());
	}
}
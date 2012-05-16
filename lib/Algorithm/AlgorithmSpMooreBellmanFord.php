<?php
class AlgorithmSpMooreBellmanFord extends AlgorithmSp{
	/**
	 * 
	 * 
	 * @param Edge $edge
	 * @param Vertex $fromVertex
	 * @param Vertex $toVertex
	 * @param array[int] $totalCostOfCheapestPathTo
	 * @param array[Vertex] $predecessorVertexOfCheapestPathTo
	 * 
	 * @return boolean
	 */
	private function doStep(& $edge, & $fromVertex, & $toVertex, & $totalCostOfCheapestPathTo, & $predecessorVertexOfCheapestPathTo){
		$isCheaper = false;
		
		if (isset($totalCostOfCheapestPathTo[$fromVertex->getId()])){			//If the fromVertex already has a path
			$newCost = $totalCostOfCheapestPathTo[$fromVertex->getId()] + $edge->getWeight(); //New possible costs of this path
	
			if (! isset($totalCostOfCheapestPathTo[$toVertex->getId()])				//No path has been found yet
					|| $totalCostOfCheapestPathTo[$toVertex->getId()] > $newCost){		//OR this path is cheaper than the old path
	
				$isCheaper = true;
				$totalCostOfCheapestPathTo[$toVertex->getId()] = $newCost;
				$predecessorVertexOfCheapestPathTo[$toVertex->getId()] = $fromVertex;
			}
		}
		
		return $isCheaper;
	}
	
	/**
	 * Calculate the Moore-Bellman-Ford-Algorithm and get all edges on shortest path for this vertex
	 * 
	 * @return array[Edge]
	 * @throws Exception if there is a negative cycle
	 */
	public function getEdges(){
		$totalCostOfCheapestPathTo  = Array();
		$totalCostOfCheapestPathTo[$this->startVertex->getId()] = 0;		    //start node distance
		
		$predecessorVertexOfCheapestPathTo  = Array();							//predecessor
		$predecessorVertexOfCheapestPathTo[$this->startVertex->getId()] = $this->startVertex;
		
		$usedVertices  = Array();												//marked vertices
		
		$totalCountOfVertices = $this->startVertex->getGraph()->getNumberOfVertices();
		$edges = $this->startVertex->getGraph()->getEdges();
		for ($i = 0; $i < $totalCountOfVertices - 1; ++$i){						//repeat n-1 times
			foreach ($edges as $edge){												//check for all edges
				foreach($edge->getTargetVertices() as $toVertex){						//check for all "ends" of this edge (or for all targetes)
					$fromVertex = $edge->getVertexFromTo($toVertex);
					
					$this->doStep($edge, $fromVertex, $toVertex, $totalCostOfCheapestPathTo, $predecessorVertexOfCheapestPathTo);	//do normal step
				}
			}
		}
		
		//algorithm is done, build graph										//THIS IS THE SAME AS DIJKSTRA (EXCTRACT TO A FUNCTION?????????)
		
		$vertices = $this->startVertex->getGraph()->getVertices();
		unset($vertices[$this->startVertex->getId()]);                          //start vertex doesn't have a predecessor
		
		$returnEdges = array();
		foreach($vertices as $vertex){
	        if (isset( $predecessorVertexOfCheapestPathTo[$vertex->getId()] )){
	            $predecessor = $predecessorVertexOfCheapestPathTo[$vertex->getId()];			//get predecor
	            
	            $returnEdges []= Edge::getFirst($predecessor->getEdgesTo($vertex),Edge::ORDER_WEIGHT);	//get cheapest edge
	        }
		}
		
		
		//Check for negative cycles
		foreach ($edges as $edge){												//check for all edges. Step n (check for negative cycles
			foreach($edge->getTargetVertices() as $toVertex){						//check for all "ends" of this edge (or for all targetes)
				$fromVertex = $edge->getVertexFromTo($toVertex);
				
				if ($this->doStep($edge, $fromVertex, $toVertex, $totalCostOfCheapestPathTo, $predecessorVertexOfCheapestPathTo)){	//If a path is getting cheaper
					//search for negative cycle
					throw new Exception("Negative Cycle TODO");
				}
			}
		}
		
		return $returnEdges;
	}
}

<?php
class AlgorithmTspNearestNeighbor{
	
	private $vertex;
	
	public function __construct(Vertex $startVertex){
 		$this->vertex = $startVertex;
	}
	
	/**
	 *
	 * @param Vertex $startVertex
	 * @return Graph
	 */
	public function getResultGraph(){
		$resultGraph = $this->vertex->getGraph()->createGraphCloneEdgeless();
		
		$n = $this->vertex->getGraph()->getNumberOfVertices();
        
		$vertex = $this->vertex;
		$visitedVertices = array($vertex->getId() => true);
		
		for (	$i = 0; $i < $n - 1; ++$i,
									$vertex = $nextVertex){						//n-1 steps (spanning tree)
			
			$edges = $vertex->getEdges();										//get all edges from the aktuel vertex
			
			$sortedEdges = new SplPriorityQueue();
			
			foreach ($edges as $edge){											//sort the edges
				$sortedEdges->insert($edge, - $edge->getWeight());
			}
			
			foreach ($sortedEdges as $edge){									//Untill first is found: get cheepest edge
				
				$nextVertex = $edge->getVertexToFrom($vertex);						//Get EndVertex of this edge
				
				if ( ! isset( $visitedVertices[ $nextVertex->getId() ] ) ){			//is unvisited
					break;
				}
			}
			
			if ( isset( $visitedVertices[ $nextVertex->getId() ] ) ){			//check if there is a way i can use
				throw new Exception("Graph is not complete - can't find an edge to unconnected vertex");
			}
			
			$visitedVertices[ $nextVertex->getId() ] = TRUE;
			
			$resultGraph->createEdgeClone($edge);								//clone edge in new Graph
			
		}
		
		//check if there is a way from end edge to start edge
		//get first connecting edge
		//connect the last vertex with the start vertex
		$resultGraph->createEdgeClone(Edge::getFirst($vertex->getEdgesTo($this->vertex)));
		
		return $resultGraph;
	}
}
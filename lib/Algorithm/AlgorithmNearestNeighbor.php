<?php
class AlgorithmNearestNeighbor{
	
	private $graph;
	
	public function __construct(Graph $inputGraph){
 		$this->graph = $inputGraph;
	}
	
	/**
	 *
	 * @param Vertex $startVertex
	 * @return Graph
	 */
	public function getResultGraph(Vertex $startVertex){
		$resultGraph = new Graph();
		$visitedVertices = array();
		
		$resultGraph->createVerticesClone( $this->graph->getVertices() );
		
		$n = count($this->graph);

		$vertex = $startVertex;
		$visitedVertices[ $vertex->getId() ] = TRUE;
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
			
			$visitedVertices[ $nextVertex->getId() ] = TRUE;
			
			$resultGraph->createEdgeClone($edge);								//clone edge in new Graph
			
		}
		
		$edges = $vertex->getEdgesTo($startVertex);
		foreach ( $edges as $edge ){											//get first connecting edge
			$resultGraph->createEdgeClone( $edge );									//connect the last vertex with the start vertex
			break;
		}
		
		return $resultGraph;
	}
}
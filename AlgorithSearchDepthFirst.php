<?php

class AlgorithSearchDepthFirst{
	
	private $StartVertexId = NULL;
	private $graph = NULL;
	private $visited = array();
		
	public function __construct($graph, $vertexId){
		$this->StartVertexId = $vertexId;
		$this->graph = $graph;
	}
	
	private function recursivDepthFirstSearch($vertexId){
		
		$vertex = $this->graph->getVertex( $vertexId );				//Get Vertex to id
		
		if ( ! isset($this->visited[$vertexId]) ){						//If I didn't visited this Vertex continue
			//echo $vertexId."\t";										//Output
			$this->visited[$vertexId] = $vertexId;					//Add Vertex to visited Vertices
			
			$edgeIds = $vertex->getEdgeIdArray();						//Get ID's of all Edges
			
			foreach ($edgeIds as $edgeId){								
				
				$edge = $this->graph->getEdge($edgeId);					//Get Edge to ID
				
				$classname = get_class($edge);
				
				if ( $classname == "EdgeUndirected" || $edge->getFromId() == $vertexId) {	//If this Edge "pointing away" of this vertex

					$nextVertices = $edge->getFromId();					//find id of next vertex
					if ($nextVertices == $vertexId){
						$nextVertices = $edge->getToId();
					}
					
					$this->recursivDepthFirstSearch( $nextVertices );	//recursiv call with next vertex
				}
			}
		}
	}
	
	public function getResult(){
		$this->recursivDepthFirstSearch((int)$this->StartVertexId);
		return $this->visited;
	}
}

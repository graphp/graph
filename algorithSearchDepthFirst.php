<?php

include_once 'vertice.php';
include_once 'graph.php';

class AlgorithSearchDepthFirst{
	
	private $StartVerticeId = NULL;
	private $graph = NULL;
	private $visited = array();
		
	public function __construct($graph, $verticeId){
		$this->StartVerticeId = $verticeId;
		$this->graph = $graph;
	}
	
	private function recursivDepthFirstSearch($verticeId){
		
		$vertice = $this->graph->getVertice( $verticeId );				//Get Vertice to id
		
		if ( ! isset($this->visited[$verticeId]) ){						//If I didn't visited this Vertice continue
			//echo $verticeId."\t";										//Output
			$this->visited[$verticeId] = $verticeId;					//Add Vertice to visited Vertices
			
			$edgeIds = $vertice->getEdgeIdArray();						//Get ID's of all Edges
			
			foreach ($edgeIds as $edgeId){								
				
				$edge = $this->graph->getEdge($edgeId);					//Get Edge to ID
				
				$classname = get_class($edge);
				
				if ( $classname == "EdgeUndirected" || $edge->getFromId() == $verticeId) {	//If this Edge "pointing away" of this vertice

					$nextVertices = $edge->getFromId();					//find id of next vertice
					if ($nextVertices == $verticeId){
						$nextVertices = $edge->getToId();
					}
					
					$this->recursivDepthFirstSearch( $nextVertices );	//recursiv call with next vertice
				}
			}
		}
	}
	
	public function getResult(){
		$this->recursivDepthFirstSearch((int)$this->StartVerticeId);
		return $this->visited;
	}
}

?>
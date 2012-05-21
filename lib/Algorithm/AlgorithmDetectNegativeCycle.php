<?php

class AlgorithmDetectNegativeCycle extends Algorithm{
	
	/**
	 * 
	 * @var Graph
	 */
	private $graph;
	
	/**
	 * 
	 * @var Vertex
	 */
	private $startVertex;
	
	/**
	 * 
	 * @param Graph $graph
	 * @param Vertex $startVertex
	 */
	public function __construct(Graph $graph, Vertex $startVertex){
		$this->graph = $graph;
		$this->startVertex = $startVertex;
	}
	
	//TODO this function
	/**
	 * Depth-first-search for a negative cycle
	 * 
	 * @param Vertex $vertex
	 * @param array $visitedVertices
	 * 
	 * @return Graph the result graph if a negative cycle is found or NULL
	 */
	private function searchNextDepth(Vertex $vertex, array $visitedVertices){
		if ( isset($visitedVertices[$vertex->getId()]) ){                       //cycle
		    $id = $vertex->getId();
		    //checke ob negativer zycle
		    
		    //baue graph zusammen
		    //gebe graph zurück
		    
		    return $graph;
		}
		
	    $vertices = $this->startVertex->getVerticesEdgeFrom();					//get next level of vertices
	    foreach ($vertices as $vertex){											//checke for all vertices if the found the negative cycle
	    	$graph = $this->searchNextDepth($vertex, $visitedVertices);
	    
	    	if ($graph != NULL){												//If they found the negative cycle return the result graph
	    		return $graph;
	    	}
	    }
	    
	    return NULL;                                                            //otherwise return NULL
	}
	
	/**
	 * Searches backwords for a negative cycle 
	 * 
	 * @param Vertex $startVertex optional
	 * 
	 * @return Graph with the negative cycle
	 * 
	 * @throws Exception if there isn't a negative cycle
	 */
	public function getNegativeCycle(){
		return null;
		
		$visitedVertices = array();
		$visitedVertices[$this->startVertex->getId()] = 0;						//Visited Vertices with the cost to ???
		
		$vertices = $this->startVertex->getVerticesEdgeFrom();					//get next level of vertices
		
		foreach ($vertices as $vertex){											//checke for all vertices if the found the negative cycle
			$graph = $this->searchNextDepth($vertex, $visitedVertices);			
			
			if ($graph != NULL){												//If they found the negative cycle return the result graph
				return $graph;
			}
		}
		
		throw  new Exception("No negative cycle found");
	}
	
}

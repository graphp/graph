<?php

class AlgorithmSearchDepthFirst{
	
	/**
	 * Start vertex for this algorithm
	 * 
	 * @var Vertex
	 */
	private $StartVertex = NULL;
	
	/**
	 * array of visited vertices
	 *
	 * @var array[Vertex]
	 */
	private $visitedVertices = array();
	
	/**
	 * 
	 * @param Vertex $startVertex
	 */
	public function __construct($startVertex){
		$this->StartVertex = $startVertex;
	}
	
	/**
	 * 
	 * calculates the recursive algorithm
	 * 
	 * fills $this->visitedVertices
	 * 
	 * @param Vertex $vertex
	 */
	private function recursiveDepthFirstSearch($vertex){
		
		if ( ! isset($this->visitedVertices[$vertex->getId()]) ){		//If I didn't visited this vertex before
			$this->visitedVertices[$vertex->getId()] = $vertex;			//Add Vertex to already visited vertices
			
			$nextVertices = $vertex->getVerticesEdgeTo();				//Get next vertices
			
			foreach ($nextVertices as $nextVertix){
				$this->recursiveDepthFirstSearch($nextVertix);				//recursive call for next vertices
			}
		}
	}

	/**
	 * 
	 * calculates a recursive depth-first search
	 * 
	 * @return array[Vertex]
	 */
	public function getResult(){
		$this->recursiveDepthFirstSearch($this->StartVertex);
		return $this->visitedVertices;
	}
}
